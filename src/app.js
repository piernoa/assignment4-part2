setTimeout(function () {
  var ele = document.getElementsByClassName("error");
  for (var i=0; i<ele.length; i++) {
    console.log(ele);
    ele[i].style.color = 'yellow';
    ele[i].style['font-size'] = '1.5em';
  }
}, 100);
