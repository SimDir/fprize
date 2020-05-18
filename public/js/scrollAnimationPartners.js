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

 

  let partnersAdvantages = document.querySelector('.advantages');
  let partnersAdvantagesContentItems = document.querySelectorAll('.advantages-content__item');
  let advantagesBtn = document.querySelector('.advantages-btn--wrap');

  let becomePartners = document.querySelector('.becomePartners');
  let becomePartnersContentItems = document.querySelectorAll('.becomePartners-content__item');
  let becomePartnersBtn = document.querySelector('.becomePartners-btn--wrap');

  let howworking = document.querySelector('.howworking');
  let howworkingTitle = document.querySelector('.howworking-title');
  let howworkingContentItems = document.querySelectorAll('.howworking-content__item');

  let tarrifs = document.querySelector('.tarrifs');
  let tarrifsTitle = document.querySelector('.tarrifs-title');
  let tarrifsTable1 = document.querySelector('.tarrifs-table-1');
  let tarrifsTable2 = document.querySelector('.tarrifs-table-2');
  let tarrifsBtn = document.querySelector('.tarrifs-btn--wrap');

  let questions = document.querySelector('.questions');
  let questionsItems = document.querySelectorAll('.questions-item');

  let footerContent = document.querySelector('.footer-content');
  let footerContentItems = document.querySelectorAll('.footer-content__item');
  function scrolling(e) {
    for(let i=0; i<partnersAdvantagesContentItems.length; i++) {
      let partnersAdvantagesContentItem = partnersAdvantagesContentItems[i];
      if (isPartiallyVisible(partnersAdvantages)) {
        partnersAdvantagesContentItems[0].classList.add("activeAdvantagesContentItem1");
        partnersAdvantagesContentItems[1].classList.add("activeAdvantagesContentItem2");
        partnersAdvantagesContentItems[2].classList.add("activeAdvantagesContentItem3");
        partnersAdvantagesContentItems[3].classList.add("activeAdvantagesContentItem4");
        advantagesBtn.classList.add("activeAdvantagesBtnwrap");
      } 
      else {
        partnersAdvantagesContentItems[0].classList.remove("activeAdvantagesContentItem1");
        partnersAdvantagesContentItems[1].classList.remove("activeAdvantagesContentItem2");
        partnersAdvantagesContentItems[2].classList.remove("activeAdvantagesContentItem3");
        partnersAdvantagesContentItems[3].classList.remove("activeAdvantagesContentItem4");
        advantagesBtn.classList.remove("activeAdvantagesBtnwrap");
      }
    }

    for(let i=0; i<becomePartnersContentItems.length; i++) {
      if (isPartiallyVisible(becomePartners)) {
        becomePartnersContentItems[0].classList.add("activeBecomePartnersContentItem1");
        becomePartnersContentItems[1].classList.add("activeBecomePartnersContentItem2");
        becomePartnersContentItems[2].classList.add("activeBecomePartnersContentItem3");
        becomePartnersBtn.classList.add('activebecomePartnersBtn');
      } 
      else {
        becomePartnersContentItems[0].classList.remove("activeBecomePartnersContentItem1");
        becomePartnersContentItems[1].classList.remove("activeBecomePartnersContentItem2");
        becomePartnersContentItems[2].classList.remove("activeBecomePartnersContentItem3");
        becomePartnersBtn.classList.remove('activebecomePartnersBtn');
      }
    }

    for(let i=0; i<howworkingContentItems.length; i++) {
      if (isPartiallyVisible(howworking)) {
        howworkingContentItems[0].classList.add('activeHowworkingContentItem1');
        howworkingContentItems[1].classList.add('activeHowworkingContentItem2');
        howworkingContentItems[2].classList.add('activeHowworkingContentItem3');
        howworkingContentItems[3].classList.add('activeHowworkingContentItem4');
        howworkingContentItems[4].classList.add('activeHowworkingContentItem5');
        howworkingTitle.classList.add('activeHowworkingTitle');
      } 
      else {
        howworkingContentItems[0].classList.remove('activeHowworkingContentItem1');
        howworkingContentItems[1].classList.remove('activeHowworkingContentItem2');
        howworkingContentItems[2].classList.remove('activeHowworkingContentItem3');
        howworkingContentItems[3].classList.remove('activeHowworkingContentItem4');
        howworkingContentItems[4].classList.remove('activeHowworkingContentItem5');        howworkingTitle.classList.remove('activeHowworkingTitle');
      }
    }
    
    if (isPartiallyVisible(tarrifs)) {
      tarrifsTable1.classList.add('activeTarrifsTable1');
      tarrifsTable2.classList.add('activeTarrifsTable2');
      tarrifsTitle.classList.add('activeTarrifsTitle');
      tarrifsBtn.classList.add('activeTarrifsBtn');
    }
    else {
      tarrifsTable1.classList.remove('activeTarrifsTable1');
      tarrifsTable2.classList.remove('activeTarrifsTable2');
      tarrifsTitle.classList.remove('activeTarrifsTitle');
      tarrifsBtn.classList.remove('activeTarrifsBtn');
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
  

    