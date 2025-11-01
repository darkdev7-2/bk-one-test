<!doctype html>
<html class="no-js" lang="zxx">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>{{ __('Finish Up') }}</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('front/digi_vault/images/favicon.svg') }}">
  <link rel="stylesheet" href="{{ asset('front/theme-2/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('front/theme-2/css/fontawesome-pro.css') }}">
  <link rel="stylesheet" href="{{ asset('front/theme-2/css/nice-select.css') }}">
  <link rel="stylesheet" href="{{ asset('front/theme-2/css/spacing.css') }}">
  <link rel="stylesheet" href="{{ asset('front/theme-2/css/styles.css') }}">
</head>

<body>
   <main>
      <section class="congratulations-section">
         <div class="js-container container">
            <div class="congratulation-wrapper">
               <div class="congratulation-contents center-text">
                <div class="checkmark-circle">
                   <div class="background"></div>
                   <div class="checkmark draw"></div>
                 </div>
                   <h3 class="congratulation-contents-title">{{ __('Congratulations') }}</h3>
                   <p class="congratulation-contents-para">
                        @if(setting('referral_signup_bonus','permission'))
                            {{ __('Congratulations! You have earned :bonus by signing up.',['bonus' => $currencySymbol.setting('signup_bonus','fee')]) }}
                        @else
                            {{ __('Congratulations! You made it.') }}
                        @endif
                   </p>
                   <div class="btn-wrapper mt-4">
                       <a href="{{ route('user.dashboard') }}" class="site-btn submit-btn aare-river">{{ __('Go to Dashboard') }}</a>
                   </div>
               </div>
           </div>
         </div>
      </section>
   </main>

   <script src="{{ asset('front/theme-2/js/jquery-3.7.1.min.js') }}"></script>
   <script src="{{ asset('front/theme-2/js/bootstrap.bundle.min.js') }}"></script>
   <script src="{{ asset('front/theme-2/js/jquery.nice-select.min.js') }}"></script>
   <script src="{{ asset('front/theme-2/js/cookie.js') }}"></script>
   <script src="{{ asset('front/theme-2/js/meanmenu.min.js') }}"></script>
   <script src="{{ asset('front/theme-2/js/main.js') }}"></script>
   <script>
      const Confettiful = function (el) {
         this.el = el;
         this.containerEl = null;

         this.confettiFrequency = 3;
         this.confettiColors = ['#EF2964', '#00C09D', '#2D87B0', '#48485E', '#EFFF1D'];
         this.confettiAnimations = ['slow', 'medium', 'fast'];

         this._setupElements();
         this._renderConfetti();
      };

      Confettiful.prototype._setupElements = function () {
         const containerEl = document.createElement('div');
         const elPosition = this.el.style.position;

         if (elPosition !== 'relative' || elPosition !== 'absolute') {
            this.el.style.position = 'relative';
         }

         containerEl.classList.add('confetti-container');

         this.el.appendChild(containerEl);

         this.containerEl = containerEl;
      };

      Confettiful.prototype._renderConfetti = function () {
         this.confettiInterval = setInterval(() => {
            const confettiEl = document.createElement('div');
            const confettiSize = (Math.floor(Math.random() * 3) + 7) + 'px';
            const confettiBackground = this.confettiColors[Math.floor(Math.random() * this.confettiColors.length)];
            const confettiLeft = (Math.floor(Math.random() * this.el.offsetWidth)) + 'px';
            const confettiAnimation = this.confettiAnimations[Math.floor(Math.random() * this.confettiAnimations.length)];

            confettiEl.classList.add('confetti', 'confetti--animation-' + confettiAnimation);
            confettiEl.style.left = confettiLeft;
            confettiEl.style.width = confettiSize;
            confettiEl.style.height = confettiSize;
            confettiEl.style.backgroundColor = confettiBackground;

            confettiEl.removeTimeout = setTimeout(function () {
               confettiEl.parentNode.removeChild(confettiEl);
            }, 3000);

            this.containerEl.appendChild(confettiEl);
         }, 25);
      };

      window.confettiful = new Confettiful(document.querySelector('.js-container'));
   </script>
</body>

</html>


