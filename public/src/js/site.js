(function(){
  "use strict";
  const body=document.body;
  const menu=document.querySelector("[data-menu-toggle]");
  const nav=document.querySelector("[data-site-nav]");
  const searchButtons=document.querySelectorAll("[data-search-toggle]");
  const searchPanel=document.querySelector("[data-search-panel]");
  const backTop=document.querySelector("[data-back-top]");

  function closeNav(){body.classList.remove("nav-open");if(menu) menu.setAttribute("aria-expanded","false");}
  if(menu){
    menu.addEventListener("click",function(){
      const open=body.classList.toggle("nav-open");
      menu.setAttribute("aria-expanded",open?"true":"false");
    });
  }
  if(nav){
    nav.addEventListener("click",function(e){if(e.target.closest("a")&&window.innerWidth<=900) closeNav();});
  }
  document.addEventListener("click",function(e){
    if(body.classList.contains("nav-open")&&window.innerWidth<=900&&!e.target.closest("[data-site-nav]")&&!e.target.closest("[data-menu-toggle]")) closeNav();
  });
  searchButtons.forEach(function(btn){
    btn.addEventListener("click",function(){
      if(!searchPanel) return;
      const open=searchPanel.classList.toggle("is-open");
      btn.setAttribute("aria-expanded",open?"true":"false");
      if(open){const input=searchPanel.querySelector("input");if(input) setTimeout(function(){input.focus();},40);}
    });
  });
  document.addEventListener("keydown",function(e){
    if(e.key==="Escape"){
      closeNav();
      if(searchPanel) searchPanel.classList.remove("is-open");
    }
  });
  if(backTop){
    const sync=function(){backTop.classList.toggle("is-visible",window.scrollY>500);};
    sync();window.addEventListener("scroll",sync,{passive:true});
    backTop.addEventListener("click",function(){window.scrollTo({top:0,behavior:"smooth"});});
  }
  const reveal=document.querySelectorAll("[data-reveal]");
  if("IntersectionObserver" in window&&reveal.length){
    const obs=new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){entry.target.style.opacity="1";entry.target.style.transform="none";obs.unobserve(entry.target);}
      });
    },{threshold:.08});
    reveal.forEach(function(el){el.style.opacity="0";el.style.transform="translateY(16px)";el.style.transition="opacity .45s ease, transform .45s ease";obs.observe(el);});
  }
})();