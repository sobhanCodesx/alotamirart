/* Hide the duplicate floating contact bar when the full resume card is visible. */
(function(){
  'use strict';
  function boot(){
    var author=document.querySelector('.alo-author-showcase');
    var bar=document.querySelector('.mobile-contact-bar');
    if(!author||!bar||!('IntersectionObserver' in window))return;
    var observer=new IntersectionObserver(function(entries){
      document.body.classList.toggle('alo-author-visible',entries[0].isIntersecting);
    },{threshold:0,rootMargin:'-5px 0px -15% 0px'});
    observer.observe(author);
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot,{once:true});
  else boot();
})();
