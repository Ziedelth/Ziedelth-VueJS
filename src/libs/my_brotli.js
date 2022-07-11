import {BrotliDecode} from "@/libs/brotli_decode";

const utf8 = require('utf8');

/**
 * @param {string} str
 * @return {!Int8Array}
 */
function stringToBytes(str) {
    let out = new Int8Array(str.length);
    for (let i = 0; i < str.length; ++i) out[i] = str.charCodeAt(i);
    return out;
}

/**
 * @param {!Int8Array} bytes
 * @return {string}
 */
function bytesToString(bytes) {
    return String.fromCharCode.apply(null, new Uint16Array(bytes));
}

function decode(str) {
    return utf8.decode(bytesToString(BrotliDecode(stringToBytes(atob(str)))));
}

export default decode