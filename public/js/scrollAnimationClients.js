document.addEventListener("DOMContentLoaded", function() {
   let isScrolling = false;
  
   window.addEventListener("scroll", throttleScroll, false);
 
   function throttleScroll(e) {
     if (isScrolling == false) {
       window.requestAnimationFrame(function() {
         scrolling(e);
         isScrolling = false;
       });
     }
     isScrolling = true;
   }
 
   // document.addEventListener("DOMContentLoaded", scrolling, false);
   let rules = document.querySelector('.rules');
   let rulesTitle = document.querySelector('.rules-title');
   let rulesContentItems = document.querySelectorAll('.rules-content__item');
   let rulesBtn = document.querySelector('.rules-btn--wrap');
   
   let price = document.querySelector('.price');
   let priceJackpot = document.querySelector('.price-jackpot');
   let priceLine1 = document.querySelector('.price-line-1');
   let priceLine2 = document.querySelector('.price-line-2');
   let priceLine3 = document.querySelector('.price-line-3');
   let priceLine4 = document.querySelector('.price-line-4');
   let priceLine5 = document.querySelector('.price-line-5');
   let priceLine6 = document.querySelector('.price-line-6');
   let priceLine7 = document.querySelector('.price-line-7');

   let winners = document.querySelector('.winners');
   let winnersTop = document.querySelector('.winners-top');
   let winnersContentTop = document.querySelector('.winners-content__top');
   let winnersContentTableRows = document.querySelector('.winners-content__table');
   let winnersContentBottom = document.querySelector('.winners-content__bottom');
 
   let questions = document.querySelector('.questions');
   let questionsItems = document.querySelectorAll('.questions-item');

   let feedback = document.querySelector('.feedback');
   let feedbackInputs = document.querySelectorAll('.feedback-wrap--input');
   let feedbackInputLast = document.querySelector('.feedback-wrap--input-last');
   let feedbackBtn = document.querySelector('.feedback-btn--wrap');

   let download = document.querySelector('.download');
   let downloadTextLine1 = document.querySelector('.download-content__left-text__line-1');
   let downloadTextLine2 = document.querySelector('.download-content__left-text__line-2');
   let downloadTextLine3 = document.querySelector('.download-content__left-text__line-3');
   let downloadContentLeftDownload = document.querySelector('.download-content__left-download');
   let downloadPhone = document.querySelector('.download-content__right');

   let footerContent = document.querySelector('.footer-content');
   let footerContentItems = document.querySelectorAll('.footer-content__item');
   function scrolling(e) {
      for(let i=0; i<rulesContentItems.length; i++) {
         if(isPartiallyVisible(rules)) {
            rulesTitle.classList.add('activeRulesTitle');
            rulesContentItems[0].classList.add('activeRulesContentItem1');
            rulesContentItems[1].classList.add('activeRulesContentItem2');
            rulesContentItems[2].classList.add('activeRulesContentItem3');
            rulesBtn.classList.add('activeRulesBtn');
         }
         else {
            rulesTitle.classList.remove('activeRulesTitle');
            rulesContentItems[0].classList.remove('activeRulesContentItem1');
            rulesContentItems[1].classList.remove('activeRulesContentItem2');
            rulesContentItems[2].classList.remove('activeRulesContentItem3');
            rulesBtn.classList.remove('activeRulesBtn');
         }
      }

      if(isPartiallyVisible(price)) {
         priceJackpot.classList.add('activePriceJackpot');
         priceLine1.classList.add('activePriceLine1');
         priceLine2.classList.add('activePriceLine2');
         priceLine3.classList.add('activePriceLine3');
         priceLine4.classList.add('activePriceLine4');
         priceLine5.classList.add('activePriceLine5');
         priceLine6.classList.add('activePriceLine6');
         priceLine7.classList.add('activePriceLine7');
      }
      else {
         priceJackpot.classList.remove('activePriceJackpot');
         priceLine1.classList.remove('activePriceLine1');
         priceLine2.classList.remove('activePriceLine2');
         priceLine3.classList.remove('activePriceLine3');
         priceLine4.classList.remove('activePriceLine4');
         priceLine5.classList.remove('activePriceLine5');
         priceLine6.classList.remove('activePriceLine6');
         priceLine7.classList.remove('activePriceLine7');
      }
      
         
      if(isPartiallyVisible(winners)) {
         winnersTop.classList.add('activeWinnersTop');
         winnersContentTop.classList.add('activeWinnersContentTop');
         winnersContentTableRows.classList.add('activeWinnersContentTable');
         winnersContentBottom.classList.add('activeWinnersContentBottom');
      }
      else {
         winnersTop.classList.remove('activeWinnersTop');
         winnersContentTop.classList.remove('activeWinnersContentTop');
         winnersContentTableRows.classList.remove('activeWinnersContentTable');
         winnersContentBottom.classList.remove('activeWinnersContentBottom');
      }


      for(let i=0; i<questionsItems.length; i++) {
         if(isPartiallyVisible(questions)) {
            questionsItems[0].classList.add('activeQuestionItem1');
            questionsItems[1].classList.add('activeQuestionItem2');
            questionsItems[2].classList.add('activeQuestionItem3');
            questionsItems[3].classList.add('activeQuestionItem4');
            questionsItems[4].classList.add('activeQuestionItem5');
            questionsItems[5].classList.add('activeQuestionItem6');
            // questionsItems[6].classList.add('activeQuestionItem7');
         }
         else {
            questionsItems[0].classList.remove('activeQuestionItem1');
            questionsItems[1].classList.remove('activeQuestionItem2');
            questionsItems[2].classList.remove('activeQuestionItem3');
            questionsItems[3].classList.remove('activeQuestionItem4');
            questionsItems[4].classList.remove('activeQuestionItem5');
            questionsItems[5].classList.remove('activeQuestionItem6');
            // questionsItems[6].classList.remove('activeQuestionItem7');
            
         }
      }
      

      for(let i=0; i<feedbackInputs.length; i++) {
         if(isPartiallyVisible(feedback)) {
            feedbackInputs[0].classList.add('activeFeedbackInput1');
            feedbackInputs[1].classList.add('activeFeedbackInput2');
            feedbackInputs[2].classList.add('activeFeedbackInput3');
            feedbackInputs[3].classList.add('activeFeedbackInput4');
            feedbackInputLast.classList.add('activeFeedbackInputLast');
            feedbackBtn.classList.add('activeFeedbackBrn');
         }
         else {
            feedbackInputs[0].classList.remove('activeFeedbackInput1');
            feedbackInputs[1].classList.remove('activeFeedbackInput2');
            feedbackInputs[2].classList.remove('activeFeedbackInput3');
            feedbackInputs[3].classList.remove('activeFeedbackInput4');
            feedbackInputLast.classList.remove('activeFeedbackInputLast');
            feedbackBtn.classList.remove('activeFeedbackBrn');
         }
      }
      

      if(isPartiallyVisible(download)) {
         downloadTextLine1.classList.add('activeDownloadTextLine1');
         downloadTextLine2.classList.add('activeDownloadTextLine2');
         downloadTextLine3.classList.add('activeDownloadTextLine3');
         downloadContentLeftDownload.classList.add('activeDownloadContentDownload');
         downloadPhone.classList.add('activeDownloadPhone');
      }
      else {
         downloadTextLine1.classList.remove('activeDownloadTextLine1');
         downloadTextLine2.classList.remove('activeDownloadTextLine2');
         downloadTextLine3.classList.remove('activeDownloadTextLine3');
         downloadContentLeftDownload.classList.remove('activeDownloadContentDownload');
         downloadPhone.classList.remove('activeDownloadPhone');
      }  
 
 
     for(let i=0; i<footerContentItems.length; i++) {
       if(isPartiallyVisible(footerContent)) {
         footerContentItems[0].classList.add('activeFooterContentItem1');
         footerContentItems[1].classList.add('activeFooterContentItem2');
         footerContentItems[2].classList.add('activeFooterContentItem3');
       }
       else {
         footerContentItems[0].classList.remove('activeFooterContentItem1');
         footerContentItems[1].classList.remove('activeFooterContentItem2');
         footerContentItems[2].classList.remove('activeFooterContentItem3');
       }
     }
   }
 
 
 
 
 
 
 
   function isPartiallyVisible(el) {
     var elementBoundary = el.getBoundingClientRect();
 
     var top = elementBoundary.top;
     var bottom = elementBoundary.bottom;
     var height = elementBoundary.height;
 
     return ((top + height >= 0) && (height + window.innerHeight >= bottom));
   }
 
   function isFullyVisible(el) {
     var elementBoundary = el.getBoundingClientRect();
 
     var top = elementBoundary.top;
     var bottom = elementBoundary.bottom;
 
     return ((top >= 0) && (bottom <= window.innerHeight));
   }
 }); 
   
 
     