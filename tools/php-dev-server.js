const { spawnSync, spawn } = require('node:child_process');
const path = require('node:path');

// Use IPv4 loopback by default to avoid IPv6-only binding surprises on Windows (curl/clients may resolve localhost to 127.0.0.1).
const host = process.env.PHP_DEV_HOST || '127.0.0.1';
const port = process.env.PHP_DEV_PORT || '8080';
const docroot = process.env.PHP_DOCROOT || '.';

function commandExists(cmd) {
  const checker = process.platform === 'win32' ? 'where' : 'which';
  const result = spawnSync(checker, [cmd], { stdio: 'ignore' });
  return result.status === 0;
}

function getPhpCommand() {
  if (process.env.PHP_PATH) {
    return process.env.PHP_PATH;
  }
  if (commandExists('php')) {
    return 'php';
  }
  return null;
}

const phpCmd = getPhpCommand();
if (!phpCmd) {
  console.error('\nPHP executable not found.');
  console.error('Install PHP and add it to PATH, or set PHP_PATH to php.exe location.');
  console.error('Example (PowerShell):');
  console.error('  $env:PHP_PATH = "C:\\xampp\\php\\php.exe"');
  console.error('  npm run dev\n');
  process.exit(1);
}

const args = ['-S', `${host}:${port}`, '-t', docroot];
if (process.env.SHMS_FLAGCOUNTER_IMG_SRC) {
  console.log('[dev] Flag Counter: SHMS_FLAGCOUNTER_IMG_SRC is set (PHP inherits this env).');
} else {
  console.log('[dev] Flag Counter: not set — use $env:SHMS_FLAGCOUNTER_IMG_SRC before npm run dev, or set flagcounter in includes/config.php.');
}
if (process.env.SHMS_SMTP_USER && process.env.SHMS_SMTP_PASS) {
  console.log('[dev] SMTP: SHMS_SMTP_USER / SHMS_SMTP_PASS are set (contact form can use Gmail).');
} else {
  console.log('[dev] SMTP: set SHMS_SMTP_USER + SHMS_SMTP_PASS or fill smtp_user/smtp_pass in includes/config.php for real mail on localhost.');
}
console.log('[dev] Registration: with webapp_url set, the site links to the Google form; optional PHP POST path if webapp_url is empty. See doc/registration-php-google-handoff.html.');
console.log(`Starting PHP dev server using: ${phpCmd} ${args.join(' ')}`);
console.log(`Trial email (PHP): http://${host}:${port}/dev-mail-test.php`);

const child = spawn(phpCmd, args, {
  cwd: path.resolve(process.cwd()),
  stdio: 'inherit',
  shell: false
});

child.on('exit', code => process.exit(code ?? 0));
