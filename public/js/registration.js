'use strict'
document.addEventListener('DOMContentLoaded', ()=>{
   
   // REGISTRATION
   let button = document.querySelector('.mainReg-form_dotSale-add-btn');

   let buttonWrap = document.querySelector('.mainReg-form_dotSale-add');
   let i = 2;
   button.onclick = function() {
      buttonWrap.insertAdjacentHTML('beforebegin', '<div class="mainReg-form_dotSale--wrap"><div class="mainReg-form_dotSale-num--wrap"><span class="mainReg-form_dotSale-num--line"></span><span class="mainReg-form_dotSale-num-title">Точка №'+i+'</span><span class="mainReg-form_dotSale-num--line"></span></div><label for="" class="mainReg-for__input--wrap"><span>Улица</span><input type="text"></label> <label for="" class="mainReg-for__input--wrap"><span>Город</span><input type="text"></label> <label for="" class="mainReg-for__input--wrap"><span>Индекс</span><input type="text"></label> <label for="" class="mainReg-for__input--wrap"><span>Страна</span><input type="text"></label> <label for="" class="mainReg-for__input--wrap"><span>Регион</span><input type="text"></label> <label for="" class="mainReg-for__input--wrap"><span>Широта и долгота распределения скидки</span><input type="text"></label> <label for="" class="mainReg-for__input--wrap"><span>Номер фискального наполнителя (КММ) №1</span><input type="text"></label></div></div>');
      i++;
   }
});
  
  
 