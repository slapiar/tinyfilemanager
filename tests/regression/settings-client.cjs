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

// Exercise the camera picker, multipart upload, cancellation and retry state.
(async () => {
  const listeners = {};
  let clicked = false;
  let uploaded;
  let destination;
  let fail = false;
  const button = {addEventListener: (event, fn) => {listeners.click = fn;}, disabled: false};
  const input = {addEventListener: (event, fn) => {listeners.change = fn;}, click: () => {clicked = true;}, files: [], value: ''};
  const status = {textContent: ''};
  context.document.getElementById = id => ({'fm-camera-button': button, 'fm-camera-input': input, 'fm-camera-status': status}[id] || null);
  context.window.location = {href: 'https://example.test/tinyfilemanager.php?p=manager%2Fphotos', assign: url => {destination = url;}};
  context.FormData = class {constructor() {this.fields = {};} append(key, value, filename) {this.fields[key] = {value, filename};}};
  context.fetch = async (url, options) => {uploaded = {url, ...options}; return {ok: !fail, json: async () => fail ? {status: 'error', info: 'Upload rejected'} : {status: 'success'}};};
  context.initCameraCapture();
  listeners.click();
  assert.equal(clicked, true);
  await listeners.change();
  assert.equal(uploaded, undefined);
  input.files = [{name: 'camera.jpg'}];
  await listeners.change();
  assert.equal(uploaded.credentials, 'same-origin');
  assert.equal(uploaded.body.fields.token.value, 'csrf-test');
  assert.equal(uploaded.body.fields.upload_dir.value, 'manager/photos');
  assert.match(uploaded.body.fields.file.filename, /^foto-.*\.jpg$/);
  assert.equal(new URL(destination).searchParams.get('p'), 'manager/photos');
  assert.equal(button.disabled, false);
  fail = true;
  destination = undefined;
  await listeners.change();
  assert.equal(destination, undefined);
  assert.equal(status.textContent, 'Upload rejected');
  assert.equal(button.disabled, false);
  console.log('PASS: camera picker, cancellation, scoped multipart upload with CSRF, and retry after failure.');
})().catch(error => {console.error(error); process.exitCode = 1;});
