(() => {
  const sel = '.contact-link';
  const scriptTag = document.querySelector(sel);
  if (!scriptTag) {
    console.error(`Could not find ${sel} selector for contact link`);
    return;
  }

  // add this tag so all content is loaded as https
  if (document.location.protocol === 'https:') {
    const meta = document.createElement('meta');
    meta.setAttribute('http-equiv', 'Content-Security-Policy');
    meta.setAttribute('content', 'upgrade-insecure-requests');
    document.head.appendChild(meta);
  }

  const {
    url: iframeUrl,
    origin,
    title: boxTitle,
    button: btnText,
  } = scriptTag.dataset;

  // create contact us button
  const button = document.createElement('a');
  button.setAttribute('href', iframeUrl);
  button.innerHTML = btnText;

  // ascertain whether we need to append the iframeSrc to current URL
  // or if we passed the absolute URL
  let url;
  if (/^https?:/.test(iframeUrl)) {
    url = new URL(iframeUrl);
  } else {
    url = new URL(document.location);
    url.pathname = iframeUrl;
  }
  url.searchParams.set('qs', url.searchParams.toString())
  url.searchParams.set('origin', origin);

  /* language=HTML */
  const html = `
    <div class="contact-backdrop">
      <div class="contact-box">
        <div class="contact-title">
          <span>${boxTitle}</span>
          <div class="contact-close">&times;</div>
        </div>
        <div class="contact-body">
          <iframe src="${url}" frameborder="0" scrolling="no"></iframe>
       </div> 
      </div>
    </div>
  `;

  let boxWrap;
  let iframe;

  const resizeBody = () => {
    const cw = iframe.contentWindow;
    // const width = cw.document.body.scrollWidth;
    // iframe.style.width = `${width}px`;
    const height = cw.document.body.scrollHeight;
    iframe.style.height = `${height}px`;
  };

  const openBox = (e) => {
    e.preventDefault();

    // add class to disable scrolling
    document.body.classList.add('modal-open');

    // create box wrap
    boxWrap = document.createElement('div');
    boxWrap.id = 'contact-box-wrap';
    boxWrap.innerHTML = html.trim();

    // close click
    const closeBtn = boxWrap.querySelector('.contact-close');
    closeBtn.addEventListener('click', closeBox);

    // prep iframe
    iframe = boxWrap.querySelector('iframe');
    iframe.addEventListener('load', () => {
      const cw = iframe.contentWindow;

      // on textarea resize
      cw.document.querySelectorAll('textarea').forEach(el => {
        new MutationObserver(resizeBody).observe(el, {
          attributes: true,
          attributeFilter: ["style"],
        });
      });

      resizeBody();
    });

    // add styles
    const style = document.createElement('link');
    style.rel = 'stylesheet';
    style.href = '/contact/contact-box.css';

    // prep backdrop
    const backdrop = boxWrap.querySelector('.contact-backdrop');
    backdrop.addEventListener('click', closeBox);

    // on window resize
    window.addEventListener('resize', resizeBody);

    // disable propagation so when clicking inside 'contact-box' it doesn't
    // close it because of bubble-up click on backdrop
    const box = boxWrap.querySelector('.contact-box');
    box.addEventListener('click', e => e.stopPropagation());

    // finally, add elements to main body
    document.head.append(style);
    document.body.append(boxWrap);
  };

  const closeBox = (e) => {
    e.preventDefault();

    // add class to disable scrolling
    document.body.classList.remove('modal-open');

    if (!boxWrap) {
      return;
    }
    boxWrap.parentNode.removeChild(boxWrap);
  };

  // button.addEventListener('click', openBox);
  scriptTag.parentNode.insertBefore(button, scriptTag.nextSibling);
})();
