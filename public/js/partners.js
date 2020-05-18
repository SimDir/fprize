'use strict'
document.addEventListener('DOMContentLoaded', ()=> {
   let w = window.screen.availWidth;
  
   let burger = document.querySelector('.header-burger');
   let line = [];
   for(let element of document.querySelectorAll('.header-burger__line')) {
      line.push(element);
   }
   let headerNav = document.querySelector('.header-nav');
   burger.onclick = function() {
      if(burger.classList.contains('burger-rotate')) {
         burger.classList.remove('burger-rotate');
         line[0].classList.add('header-burger__line');
         line[0].classList.remove('line1-rotate');
         line[1].classList.remove('line2-rotate');
         line[2].classList.add('header-burger__line');
         line[2].classList.remove('line3-rotate');
         headerNav.style.transform = "translateX(100vw) scale(0)";
     }
     else {
         burger.classList.add('burger-rotate')
         line[0].classList.remove('header-burger__line');
         line[0].classList.add('line1-rotate');
         line[1].classList.add('line2-rotate');
         line[2].classList.remove('header-burger__line');
         line[2].classList.add('line3-rotate');
         headerNav.style.transform = "translateX(0) scale(1)";
     }
   }



   let questionItem = [];
   for(let element of document.querySelectorAll('.questions-item__question')) {
      questionItem.push(element)
   }

   let questionItemText = [];
   for(let element of document.querySelectorAll('.questions-item__question p')) {
      questionItemText.push(element);
   }

   let answerItem = [];
   for(let element of document.querySelectorAll('.questions-item__answer')) {
      answerItem.push(element);
   }

   let questionPlus = [];
   for(let element of document.querySelectorAll('.questions-item__question-plus--wrap')) {
      questionPlus.push(element);
   }

   let questionLine = [];
   for(let element of document.querySelectorAll(' .question-plus-line-2')) {
      questionLine.push(element);
   }
   questionItem[0].onclick=function() {
      if(questionPlus[0].classList.contains('questionActive')) {
         questionPlus[0].classList.remove('questionActive');
         questionLine[0].style.transform = "rotate(90deg)";
         questionLine[0].style.background = "#9400FF";
         this.style.background = "transparent";
         questionItemText[0].style.color = "#000";
         answerItem[0].style.height = "0";
         answerItem[0].style.padding = "0";
      }
      else {
         questionPlus[0].classList.add('questionActive');
         questionLine[0].style.transform = "rotate(0)";
         questionLine[0].style.background = "#fff";
         this.style.background = "#9400FF";
         questionItemText[0].style.color = "#fff";
         answerItem[0].style.height = "initial";
         answerItem[0].style.padding = "10px 30px";
      }
   }
   questionItem[1].onclick=function() {
      if(questionPlus[1].classList.contains('questionActive')) {
         questionPlus[1].classList.remove('questionActive');
         questionLine[1].style.transform = "rotate(90deg)";
         questionLine[1].style.background = "#9400FF";
         this.style.background = "transparent";
         questionItemText[1].style.color = "#000";
         answerItem[1].style.height = "0";
         answerItem[1].style.padding = "0";
      }
      else {
         questionPlus[1].classList.add('questionActive');
         questionLine[1].style.transform = "rotate(0)";
         questionLine[1].style.background = "#fff";
         this.style.background = "#9400FF";
         questionItemText[1].style.color = "#fff";
         answerItem[1].style.height = "initial";
         answerItem[1].style.padding = "10px 30px";
      }
   }
   questionItem[2].onclick=function() {
      if(questionPlus[2].classList.contains('questionActive')) {
         questionPlus[2].classList.remove('questionActive');
         questionLine[2].style.transform = "rotate(90deg)";
         questionLine[2].style.background = "#9400FF";
         this.style.background = "transparent";
         questionItemText[2].style.color = "#000";
         answerItem[2].style.height = "0";
         answerItem[2].style.padding = "0";
      }
      else {
         questionPlus[2].classList.add('questionActive');
         questionLine[2].style.transform = "rotate(0)";
         questionLine[2].style.background = "#fff";
         this.style.background = "#9400FF";
         questionItemText[2].style.color = "#fff";
         answerItem[2].style.height = "initial";
         answerItem[2].style.padding = "10px 30px";
      }
   }
   questionItem[3].onclick=function() {
      if(questionPlus[3].classList.contains('questionActive')) {
         questionPlus[3].classList.remove('questionActive');
         questionLine[3].style.transform = "rotate(90deg)";
         questionLine[3].style.background = "#9400FF";
         this.style.background = "transparent";
         questionItemText[3].style.color = "#000";
         answerItem[3].style.height = "0";
         answerItem[3].style.padding = "0";
      }
      else {
         questionPlus[3].classList.add('questionActive');
         questionLine[3].style.transform = "rotate(0)";
         questionLine[3].style.background = "#fff";
         this.style.background = "#9400FF";
         questionItemText[3].style.color = "#fff";
         answerItem[3].style.height = "initial";
         answerItem[3].style.padding = "10px 30px";
      }
   }
   questionItem[4].onclick=function() {
      if(questionPlus[4].classList.contains('questionActive')) {
         questionPlus[4].classList.remove('questionActive');
         questionLine[4].style.transform = "rotate(90deg)";
         questionLine[4].style.background = "#9400FF";
         this.style.background = "transparent";
         questionItemText[4].style.color = "#000";
         answerItem[4].style.height = "0";
         answerItem[4].style.padding = "0";
      }
      else {
         questionPlus[4].classList.add('questionActive');
         questionLine[4].style.transform = "rotate(0)";
         questionLine[4].style.background = "#fff";
         this.style.background = "#9400FF";
         questionItemText[4].style.color = "#fff";
         answerItem[4].style.height = "initial";
         answerItem[4].style.padding = "10px 30px";
      }
   }
   questionItem[5].onclick=function() {
      if(questionPlus[5].classList.contains('questionActive')) {
         questionPlus[5].classList.remove('questionActive');
         questionLine[5].style.transform = "rotate(90deg)";
         questionLine[5].style.background = "#9400FF";
         this.style.background = "transparent";
         questionItemText[5].style.color = "#000";
         answerItem[5].style.height = "0";
         answerItem[5].style.padding = "0";
      }
      else {
         questionPlus[5].classList.add('questionActive');
         questionLine[5].style.transform = "rotate(0)";
         questionLine[5].style.background = "#fff";
         this.style.background = "#9400FF";
         questionItemText[5].style.color = "#fff";
         answerItem[5].style.height = "initial";
         answerItem[5].style.padding = "10px 30px";
      }
   }
   // questionItem[6].onclick=function() {
   //    if(questionPlus[6].classList.contains('questionActive')) {
   //       questionPlus[6].classList.remove('questionActive');
   //       questionLine[6].style.transform = "rotate(90deg)";
   //       questionLine[6].style.background = "#9400FF";
   //       this.style.background = "transparent";
   //       questionItemText[6].style.color = "#000";
   //       answerItem[6].style.height = "0";
   //       answerItem[6].style.padding = "0";
   //    }
   //    else {
   //       questionPlus[6].classList.add('questionActive');
   //       questionLine[6].style.transform = "rotate(0)";
   //       questionLine[6].style.background = "#fff";
   //       this.style.background = "#9400FF";
   //       questionItemText[6].style.color = "#fff";
   //       answerItem[6].style.height = "initial";
   //       answerItem[6].style.padding = "10px 30px";
   //    }
   // }


   let rowTable= document.querySelectorAll('.tariffs-row-1');
   let tarrifsBtn = document.querySelector('.tarrifs-btn--wrap a');
   for(let i=0; i<rowTable.length; i++) {
      rowTable[i].onclick = function(e) {
         for(let i=0; i<rowTable.length; i++) {
            rowTable[i].style.background = "transparent";
         }
         this.style.background = "#FFD900";
         tarrifsBtn.style.opacity = "1"; 
//         tarrifsBtn.setAttribute('disable', 'false');
         tarrifsBtn.setAttribute('href', '/user/registre/partner');
      }
   }
   
   // let rowsTable1=$( ".tariffs-row-1" );
   // $( ".tariffs-row-1" ).click(function(e) {
   //    for(let i=0; i<rowsTable1.length; i++) {
   //       rowsTable1.css("background", "#FFD900");
   //    }
   //    $( e.target.parentNode  ).css("background", "red");
   //    console.log(e.target.parentNode );
   // });
});