/* Accessible Explore-style preview for AloTamiratchi cards.
   Original links remain ordinary links when JavaScript is disabled. */
(function(){
  "use strict";
  if(!window.HTMLDialogElement || typeof window.HTMLDialogElement.prototype.showModal!=="function") return;

  var rootSelectors=".content-grid, .alo-brand-grid, .brand-grid";
  var itemSelector="article.content-card, a.alo-brand-card, a.brand-card";
  var popup=null;
  var items=[];
  var current=0;
  var opener=null;
  var touchStart=null;

  function getCard(anchor){
    var card=anchor.closest(itemSelector);
    return card && card.closest(rootSelectors) ? card : null;
  }
  function getLink(card){
    if(card.matches("a[href]")) return card;
    return card.querySelector("a.content-card__link[href],a.content-card__media[href],.content-card__body h3 a[href],a[href]");
  }
  function getItem(card){
    var link=getLink(card);
    if(!link) return null;
    var url;
    try{url=new URL(link.getAttribute("href"),window.location.href);}catch(error){return null;}
    if(url.origin!==window.location.origin || !/^https?:$/.test(url.protocol)) return null;
    var photo=card.querySelector("img");
    var title=card.querySelector(".alo-brand-card__title,.content-card__title,.content-card h3,.brand-card strong");
    var description=card.querySelector(".alo-brand-card__description,.content-card__summary,.content-card__body p,.brand-card span");
    var meta=card.querySelector(".content-card__meta");
    return {
      card:card,
      url:url.href,
      photo:photo ? (photo.currentSrc || photo.getAttribute("src") || "") : "",
      title:(title ? title.textContent : (photo ? photo.alt : "نمایش محتوا")).trim(),
      description:(description ? description.textContent : "").trim(),
      kind:meta ? meta.textContent.trim() : (card.closest(".alo-brand-grid,.brand-grid") ? "خدمات و برندها" : "مقالات و راهنماها")
    };
  }
  function itemsInGrid(grid){
    var seen=new Set(),result=[];
    grid.querySelectorAll(itemSelector).forEach(function(card){
      var item=getItem(card);
      if(item && !seen.has(item.url)){
        seen.add(item.url);
        result.push(item);
      }
    });
    return result;
  }
  function createPopup(){
    if(popup) return popup;
    popup=document.createElement("dialog");
    popup.className="alo-explore";
    popup.setAttribute("aria-labelledby","alo-explore-title");
    popup.innerHTML=
      '<div class="alo-explore__shell">'+
        '<div class="alo-explore__media" data-explore-swipe>'+
          '<img class="alo-explore__ambient" alt="" aria-hidden="true">'+
          '<img class="alo-explore__image" alt="">'+
          '<div class="alo-explore__top">'+
            '<span class="alo-explore__counter" data-explore-count></span>'+
            '<button class="alo-explore__close" type="button" data-explore-close aria-label="بستن پیش‌نمایش">×</button>'+
          '</div>'+
          '<button class="alo-explore__nav alo-explore__nav--previous" type="button" data-explore-prev aria-label="مورد قبلی">›</button>'+
          '<button class="alo-explore__nav alo-explore__nav--next" type="button" data-explore-next aria-label="مورد بعدی">‹</button>'+
        '</div>'+
        '<div class="alo-explore__copy">'+
          '<span class="alo-explore__eyebrow" data-explore-kind></span>'+
          '<h2 class="alo-explore__title" id="alo-explore-title"></h2>'+
          '<p class="alo-explore__description" data-explore-description></p>'+
          '<div class="alo-explore__copy-footer">'+
            '<a class="alo-explore__open" data-explore-open href="#">مشاهده صفحه کامل <span aria-hidden="true">←</span></a>'+
            '<span class="alo-explore__hint">برای دیدن موارد دیگر ورق بزنید یا از فلش‌ها استفاده کنید.</span>'+
          '</div>'+
        '</div>'+
      '</div>';
    document.body.appendChild(popup);
    popup.querySelector("[data-explore-close]").addEventListener("click",function(){popup.close();});
    popup.querySelector("[data-explore-next]").addEventListener("click",function(){move(1);});
    popup.querySelector("[data-explore-prev]").addEventListener("click",function(){move(-1);});
    popup.addEventListener("click",function(event){if(event.target===popup) popup.close();});
    popup.addEventListener("close",function(){
      document.body.classList.remove("alo-explore-open");
      if(opener && opener.isConnected && typeof opener.focus==="function"){
        try{opener.focus({preventScroll:true});}catch(error){opener.focus();}
      }
      opener=null;
    });
    var swipe=popup.querySelector("[data-explore-swipe]");
    swipe.addEventListener("touchstart",function(e){
      if(e.touches.length!==1) return;
      touchStart={x:e.touches[0].clientX,y:e.touches[0].clientY};
    },{passive:true});
    swipe.addEventListener("touchend",function(e){
      if(!touchStart || !e.changedTouches.length) return;
      var deltaX=e.changedTouches[0].clientX-touchStart.x;
      var deltaY=e.changedTouches[0].clientY-touchStart.y;
      touchStart=null;
      if(Math.abs(deltaX)>55 && Math.abs(deltaX)>Math.abs(deltaY)*1.4){
        move(deltaX>0?1:-1);
      }
    },{passive:true});
    return popup;
  }
  function render(){
    if(!popup || !items.length) return;
    var item=items[current];
    var main=popup.querySelector(".alo-explore__image");
    var ambient=popup.querySelector(".alo-explore__ambient");
    main.hidden=!item.photo;
    ambient.hidden=!item.photo;
    if(item.photo){
      main.src=item.photo;
      ambient.src=item.photo;
      main.alt=item.title;
    }else{
      main.removeAttribute("src");
      ambient.removeAttribute("src");
    }
    popup.querySelector(".alo-explore__counter").textContent=(current+1).toLocaleString("fa-IR")+" / "+items.length.toLocaleString("fa-IR");
    popup.querySelector("[data-explore-kind]").textContent=item.kind;
    popup.querySelector(".alo-explore__title").textContent=item.title;
    popup.querySelector("[data-explore-description]").textContent=item.description || "برای اطلاعات بیشتر و مشاهده مطالب مرتبط، صفحه کامل را باز کنید.";
    popup.querySelector("[data-explore-open]").href=item.url;
    popup.querySelector("[data-explore-next]").hidden=items.length<2;
    popup.querySelector("[data-explore-prev]").hidden=items.length<2;
  }
  function move(delta){
    if(!popup || !popup.open || items.length<2) return;
    current=(current+delta+items.length)%items.length;
    render();
  }
  function openFor(card,clicked){
    var grid=card.closest(rootSelectors);
    if(!grid) return false;
    var listing=itemsInGrid(grid);
    var index=listing.findIndex(function(item){return item.card===card;});
    if(index<0) return false;
    items=listing;
    current=index;
    opener=clicked;
    createPopup();
    render();
    try{
      popup.showModal();
      document.body.classList.add("alo-explore-open");
      popup.querySelector("[data-explore-close]").focus();
      return true;
    }catch(error){
      opener=null;
      return false;
    }
  }
  document.addEventListener("click",function(event){
    if(event.defaultPrevented || event.button!==0 || event.ctrlKey || event.metaKey || event.altKey || event.shiftKey) return;
    var target=event.target;
    if(!target || typeof target.closest!=="function") return;
    var link=target.closest("a[href]");
    if(!link || link.hasAttribute("download") || (link.target && link.target!=="_self")) return;
    var card=getCard(link);
    if(!card || !getItem(card)) return;
    if(openFor(card,link)) event.preventDefault();
  });
  document.addEventListener("keydown",function(event){
    if(!popup || !popup.open) return;
    if(event.key==="ArrowLeft"){event.preventDefault();move(1);}
    else if(event.key==="ArrowRight"){event.preventDefault();move(-1);}
    // Native dialog handles Escape and focus trapping.
  });
  document.querySelectorAll(".content-grid a[href],.alo-brand-grid a[href],.brand-grid a[href]").forEach(function(link){
    if(getCard(link)) link.setAttribute("aria-haspopup","dialog");
  });
})();
