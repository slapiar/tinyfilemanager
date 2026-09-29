// Execute the real asset with classic-script semantics, as loaded by the PHP layout.
const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const path = require('node:path');
let serialized = '';
let sent;
const form = {
  serialize: () => serialized,
  find: (selector) => ({val: () => selector.includes('token') ? 'csrf-test' : 'dark'}),
  attr: (name) => name === 'method' ? 'post' : '',
  toggleClass: () => {},
  ready: () => {},
  on: () => {},
  off: () => {},
};
function jquery(value) { return typeof value === 'function' ? undefined : form; }
jquery.extend = (...objects) => Object.assign({}, ...objects);
jquery.ajax = (options) => { sent = options; };
const context = {
  window: {csrf: 'csrf-test'},
  document: {
    getElementById: () => null,
    querySelector: () => null,
    readyState: 'loading',
    addEventListener: () => {},
    documentElement: {setAttribute: () => {}},
  },
  $: jquery,
  jQuery: jquery,
  URL,
  setTimeout,
  console,
};
vm.createContext(context);
new vm.Script(fs.readFileSync(path.join(__dirname, '../../src/assets/js/fm-main.js'), 'utf8')).runInContext(context);
assert.equal(typeof context.window.save_settings, 'function');
for (const enabled of [true, false]) {
  serialized = 'type=settings&js-theme-3=dark&js-list-density=compact';
  if (enabled) serialized += '&js-fallback-log-enabled=true';
  assert.equal(context.window.save_settings(form), false);
  const data = new URLSearchParams(sent.data);
  assert.equal(data.get('js-fallback-log-enabled'), enabled ? 'true' : null);
  assert.equal(data.get('token'), 'csrf-test');
  assert.equal(data.get('ajax'), 'true');
}
console.log('PASS: classic browser script loads and settings sends fallback switch with CSRF.');
