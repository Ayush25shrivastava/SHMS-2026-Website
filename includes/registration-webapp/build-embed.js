const fs = require('fs');
const path = require('path');
const src = path.join(__dirname, 'Registration.html.txt');
const out = path.join(__dirname, '_registration_embedded.gs.fragment');
let s = fs.readFileSync(src, 'utf8');
s = s.replace(/\\/g, '\\\\').replace(/`/g, '\\`').replace(/\$\{/g, '\\${');
const body = 'function registrationHtmlSource_() {\n  return `' + s + '`;\n}\n';
fs.writeFileSync(out, body, 'utf8');
console.log('Wrote', out, body.length, 'chars');
