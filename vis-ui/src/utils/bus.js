import Vue from 'vue';
const Bus = new Vue({});

window. addEventListener('blur',() =>{
    Bus.$emit('window-blur');
});

export default Bus;