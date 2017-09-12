import Vue from 'Vue'
const Bus = new Vue()
export default Bus;
window.$bus = Bus;