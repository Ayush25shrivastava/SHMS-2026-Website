"""
Generate a hero-banner collage image for the SHMS-2026 website.

Usage (from this directory):
    python generate_hero_collage.py input_folder assets/images/mnnit-campus.png

Where:
  - input_folder contains 3–6 campus photos (JPG/PNG) of MNNIT
  - the output PNG is written at the path used by the site's CSS hero background
"""

import sys
from pathlib import Path

from PIL import Image


def build_collage(input_dir: Path, output_path: Path, width: int = 1600, height: int = 900) -> None:
    images = []
    for ext in ("*.jpg", "*.jpeg", "*.png"):
        images.extend(sorted(input_dir.glob(ext)))

    if len(images) < 3:
        raise SystemExit(
            f"Need at least 3 images in {input_dir} (found {len(images)}). "
            "Add more campus photos and rerun."
        )

    # Load and convert to RGB
    loaded = [Image.open(p).convert("RGB") for p in images]

    # Create base canvas
    canvas = Image.new("RGB", (width, height), (0, 0, 0))

    # Strategy:
    # - use first image as main center background (cover)
    # - overlay up to 3 others as side/bottom panels with slight transparency

    def paste_cover(img: Image.Image, box_w: int, box_h: int) -> Image.Image:
        """Resize image to cover target box while preserving aspect ratio."""
        src_w, src_h = img.size
        scale = max(box_w / src_w, box_h / src_h)
        new_size = (int(src_w * scale), int(src_h * scale))
        resized = img.resize(new_size, Image.LANCZOS)
        # center crop
        x0 = (resized.width - box_w) // 2
        y0 = (resized.height - box_h) // 2
        return resized.crop((x0, y0, x0 + box_w, y0 + box_h))

    # Main background
    main_bg = paste_cover(loaded[0], width, height)
    canvas.paste(main_bg, (0, 0))

    # Helper to paste with transparency
    def blend_panel(panel_img: Image.Image, x: int, y: int, w: int, h: int, alpha: float) -> None:
        panel = paste_cover(panel_img, w, h)
        panel = panel.convert("RGBA")
        # Apply alpha
        r, g, b, _ = panel.split()
        a = Image.new("L", panel.size, int(255 * alpha))
        panel = Image.merge("RGBA", (r, g, b, a))

        base = canvas.convert("RGBA")
        base.paste(panel, (x, y), panel)
        canvas.paste(base.convert("RGB"), (0, 0))

    # Overlay side/bottom panels if available
    if len(loaded) >= 2:
        # Left panel
        blend_panel(loaded[1], 0, height // 4, width // 3, height // 2, alpha=0.55)
    if len(loaded) >= 3:
        # Right panel
        blend_panel(loaded[2], width - width // 3, height // 4, width // 3, height // 2, alpha=0.55)
    if len(loaded) >= 4:
        # Bottom strip
        blend_panel(loaded[3], width // 4, int(height * 0.55), width // 2, int(height * 0.35), alpha=0.45)

    output_path.parent.mkdir(parents=True, exist_ok=True)
    canvas.save(output_path, format="PNG", optimize=True)
    print(f"Saved collage hero image to: {output_path}")


def main(argv: list[str]) -> None:
    if len(argv) != 3:
        print("Usage: python generate_hero_collage.py <input_folder> <output_path>", file=sys.stderr)
        raise SystemExit(1)

    input_dir = Path(argv[1]).resolve()
    output_path = Path(argv[2]).resolve()

    if not input_dir.is_dir():
        raise SystemExit(f"Input folder does not exist or is not a directory: {input_dir}")

    build_collage(input_dir, output_path)


if __name__ == "__main__":
    main(sys.argv)

