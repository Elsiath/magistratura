var date = new Date();
if ((parseInt(date.getHours()) >= 19)&&(parseInt(date.getHours()) >= 25)){
    alert('1');
    document.getElementById('dates').innerHTML =  'Вебинар завтра в 19:00 МСК';
}
