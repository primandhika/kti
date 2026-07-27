const deckFooterLogo = document.getElementById('deck-fixed-footer-logo');
const deckLogoSources = document.querySelectorAll('img[alt="Logo UHAMKA"]');
const deckSourceLogo = deckLogoSources[deckLogoSources.length - 1];
if (deckFooterLogo && deckSourceLogo) {
  deckFooterLogo.src = deckSourceLogo.src;
}

Reveal.initialize({
  hash: true,
  slideNumber: 'c/t',
  progress: true,
  center: false,
  controls: true,
  controlsTutorial: false,
  transition: 'fade',
  backgroundTransition: 'fade',
  transitionSpeed: 'slow',
  width: 1200,
  height: 700,
  margin: 0.06,
  minScale: 0.5,
  maxScale: 1.5,
  autoAnimateEasing: 'ease-in-out',
  autoAnimateDuration: 0.8,
});
