// Get the screen width of the device
var device_width = window.screen.width;

// Select all elements with the class "has_text_move_anim" for text animation
let text_animation = gsap.utils.toArray(".has_text_move_anim");
if (text_animation) {
  // Loop through each selected element
  text_animation.forEach(function (t) {
    // Set default delay, or get from data attribute if specified
    var delay = 0.1;
    if (t.getAttribute("data-delay")) {
      delay = t.getAttribute("data-delay");
    }

    // Create a GSAP timeline with scroll trigger for each element
    var timeline = gsap.timeline({
      scrollTrigger: {
        trigger: t, // The element to watch for scroll position
        start: "top 85%", // Start animation when element reaches 85% from the top of viewport
        duration: 1, // Animation duration
        scrub: false, // No smooth scrubbing effect
        markers: false, // No visual markers for scroll trigger
        toggleActions: "play none none none" // Play animation on scroll, no other actions
      }
    });

    // Use SplitText to split text into lines for animation
    var splitText = new SplitText(t, { type: "lines" });
    gsap.set(t, { perspective: 400 }); // Set perspective for 3D effect
    splitText.split({ type: "lines" });

    // Animate each line from rotation with opacity fade in
    timeline.from(splitText.lines, {
      duration: 1, // Animation duration for each line
      delay: delay, // Delay for each animation
      opacity: 0, // Start with invisible lines
      rotationX: -80, // Rotate lines on X axis
      force3D: true, // Enable 3D transformation
      transformOrigin: "top center -50", // Set transform origin for rotation
      stagger: 0.1 // Delay between each line's animation
    });
  });
}

// Select all elements with the class "has_char_anim" for character animation
var animation_char_come_items = document.querySelectorAll(".has_char_anim");
animation_char_come_items.forEach(function (t) {
  // Set default animation parameters and get values from data attributes if provided
  var stagger = 0.05, translateX = 20, translateY = false, onScroll = 1, delay = 0.1, duration = 1, ease = "power2.out";

  if (t.getAttribute("data-stagger")) {
    stagger = t.getAttribute("data-stagger");
  }
  if (t.getAttribute("data-translateX")) {
    translateX = t.getAttribute("data-translateX");
  }
  if (t.getAttribute("data-translateY")) {
    translateY = t.getAttribute("data-translateY");
  }
  if (t.getAttribute("data-on-scroll")) {
    onScroll = t.getAttribute("data-on-scroll");
  }
  if (t.getAttribute("data-delay")) {
    delay = t.getAttribute("data-delay");
  }
  if (t.getAttribute("data-ease")) {
    ease = t.getAttribute("data-ease");
  }
  if (t.getAttribute("data-duration")) {
    duration = t.getAttribute("data-duration");
  }

  // Handle animations when on-scroll is enabled
  if (onScroll == 1) {
    // Translate animation on the X axis
    if (translateX > 0 && !translateY) {
      var splitTextX = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextX.chars, {
        duration: duration,
        delay: delay,
        x: translateX,
        autoAlpha: 0,
        stagger: stagger,
        ease: ease,
        scrollTrigger: {
          trigger: t,
          start: "top 85%" // Start animation when element reaches 85% from the top
        }
      });
    }
    // Translate animation on the Y axis
    if (translateY > 0 && !translateX) {
      var splitTextY = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextY.chars, {
        duration: duration,
        delay: delay,
        y: translateY,
        autoAlpha: 0,
        ease: ease,
        stagger: stagger,
        scrollTrigger: {
          trigger: t,
          start: "top 85%"
        }
      });
    }
    // Translate animation on both X and Y axes
    if (translateX && translateY) {
      var splitTextXY = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextXY.chars, {
        duration: 2,
        delay: delay,
        y: translateY,
        x: translateX,
        autoAlpha: 0,
        ease: ease,
        stagger: stagger,
        scrollTrigger: {
          trigger: t,
          start: "top 85%"
        }
      });
    }
    // Default translation animation on X axis
    if (!translateX && !translateY) {
      var splitTextDefault = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextDefault.chars, {
        duration: 1,
        delay: delay,
        x: 50,
        autoAlpha: 0,
        stagger: stagger,
        ease: ease,
        scrollTrigger: {
          trigger: t,
          start: "top 85%"
        }
      });
    }
  } else {
    // Handle animations when not triggered by scroll
    if (translateX > 0 && !translateY) {
      var splitTextNoScrollX = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextNoScrollX.chars, {
        duration: 1,
        delay: delay,
        x: translateX,
        ease: ease,
        autoAlpha: 0,
        stagger: stagger
      });
    }
    if (translateY > 0 && !translateX) {
      var splitTextNoScrollY = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextNoScrollY.chars, {
        duration: 1,
        delay: delay,
        y: translateY,
        autoAlpha: 0,
        ease: ease,
        stagger: stagger
      });
    }
    if (translateX && translateY) {
      var splitTextNoScrollXY = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextNoScrollXY.chars, {
        duration: 1,
        delay: delay,
        x: translateX,
        y: translateY,
        ease: ease,
        autoAlpha: 0,
        stagger: stagger
      });
    }
    if (!translateX && !translateY) {
      var splitTextNoScrollDefault = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextNoScrollDefault.chars, {
        duration: 1,
        delay: delay,
        ease: ease,
        x: 50,
        autoAlpha: 0,
        stagger: stagger
      });
    }
  }
});

// Select all elements with the class "has_word_anim" for word animation
let animation_word_anim_items = document.querySelectorAll(".has_word_anim");
animation_word_anim_items.forEach(function (t) {
  // Set default animation parameters and get values from data attributes if provided
  var stagger = 0.04, translateX = false, translateY = false, onScroll = 1, delay = 0.1, duration = 0.75;

  if (t.getAttribute("data-stagger")) {
    stagger = t.getAttribute("data-stagger");
  }
  if (t.getAttribute("data-translateX")) {
    translateX = t.getAttribute("data-translateX");
  }
  if (t.getAttribute("data-translateY")) {
    translateY = t.getAttribute("data-translateY");
  }
  if (t.getAttribute("data-on-scroll")) {
    onScroll = t.getAttribute("data-on-scroll");
  }
  if (t.getAttribute("data-delay")) {
    delay = t.getAttribute("data-delay");
  }
  if (t.getAttribute("data-duration")) {
    duration = t.getAttribute("data-duration");
  }

  // Handle animations when on-scroll is enabled
  if (onScroll == 1) {
    // Translate animation on the X axis
    if (translateX && !translateY) {
      var splitTextX = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextX.words, {
        duration: duration,
        x: translateX,
        autoAlpha: 0,
        stagger: stagger,
        delay: delay,
        scrollTrigger: {
          trigger: t,
          start: "top 90%" // Start animation when element reaches 90% from the top
        }
      });
    }
    // Translate animation on the Y axis
    if (translateY && !translateX) {
      var splitTextY = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextY.words, {
        duration: 1,
        delay: delay,
        y: translateY,
        autoAlpha: 0,
        stagger: stagger,
        scrollTrigger: {
          trigger: t,
          start: "top 90%"
        }
      });
    }
    // Translate animation on both X and Y axes
    if (translateX && translateY) {
      var splitTextXY = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextXY.words, {
        duration: 2,
        x: translateX,
        y: translateY,
        autoAlpha: 0,
        stagger: stagger,
        delay: delay,
        scrollTrigger: {
          trigger: t,
          start: "top 90%"
        }
      });
    }
    // Default translation animation on X axis
    if (!translateX && !translateY) {
      var splitTextDefault = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextDefault.words, {
        duration: 1,
        x: 20,
        autoAlpha: 0,
        stagger: stagger,
        delay: delay,
        scrollTrigger: {
          trigger: t,
          start: "top 90%"
        }
      });
    }
  } else {
    // Handle animations when not triggered by scroll
    if (translateX && !translateY) {
      var splitTextNoScrollX = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextNoScrollX.words, {
        duration: 1,
        delay: delay,
        x: translateX,
        autoAlpha: 0,
        stagger: stagger
      });
    }
    if (translateY && !translateX) {
      var splitTextNoScrollY = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextNoScrollY.words, {
        duration: 1,
        delay: delay,
        y: translateY,
        autoAlpha: 0,
        stagger: stagger
      });
    }
    if (translateX && translateY) {
      var splitTextNoScrollXY = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextNoScrollXY.words, {
        duration: 1,
        delay: delay,
        x: translateX,
        y: translateY,
        autoAlpha: 0,
        stagger: stagger
      });
    }
    if (!translateX && !translateY) {
      var splitTextNoScrollDefault = new SplitText(t, { type: "chars, words" });
      gsap.from(splitTextNoScrollDefault.words, {
        duration: 1,
        delay: delay,
        x: 20,
        autoAlpha: 0,
        stagger: stagger
      });
    }
  }
});

// Select all elements with the class "has_fade_anim" for fade animations
let fadeArray_items = document.querySelectorAll(".has_fade_anim");
if (fadeArray_items.length > 0) {
  // Use GSAP utility to turn NodeList into array
  let fadeItemsArray = gsap.utils.toArray(".has_fade_anim");
  fadeItemsArray.forEach(function (t, a) {
    // Set default fade animation parameters and get values from data attributes if provided
    var fadeFrom = "bottom", duration = 1, fadeDistance = 50, delay = 0.15, ease = "power2.out", onScroll = 1;

    if (t.getAttribute("data-fade-offset")) {
      fadeDistance = t.getAttribute("data-fade-offset");
    }
    if (t.getAttribute("data-duration")) {
      duration = t.getAttribute("data-duration");
    }
    if (t.getAttribute("data-fade-from")) {
      fadeFrom = t.getAttribute("data-fade-from");
    }
    if (t.getAttribute("data-on-scroll")) {
      onScroll = t.getAttribute("data-on-scroll");
    }
    if (t.getAttribute("data-delay")) {
      delay = t.getAttribute("data-delay");
    }
    if (t.getAttribute("data-ease")) {
      ease = t.getAttribute("data-ease");
    }

    // Configure fade options based on direction of fade (top, left, bottom, right)
    let fadeOptions = {
      opacity: 0,
      ease: ease,
      duration: duration,
      delay: delay
    };
    if (fadeFrom == "top") {
      fadeOptions.y = -fadeDistance;
    }
    if (fadeFrom == "left") {
      fadeOptions.x = -fadeDistance;
    }
    if (fadeFrom == "bottom") {
      fadeOptions.y = fadeDistance;
    }
    if (fadeFrom == "right") {
      fadeOptions.x = fadeDistance;
    }

    // If fade animation is triggered by scroll
    if (onScroll == 1) {
      fadeOptions.scrollTrigger = {
        trigger: t, // Element to watch for scroll position
        start: "top 85%" // Start fade animation when element reaches 85% from the top
      };
    }

    // Apply GSAP fade animation with the configured options
    gsap.from(t, fadeOptions);
  });
}

// Pin Active
var pin_fixed = document.querySelectorAll('.pin__element');
var pin__area = document.querySelectorAll('.pin__area');

if (pin_fixed && device_width > 991) {

  pin_fixed.forEach((element, index) => {

    gsap.to(element, {
      scrollTrigger: {
        trigger: pin__area[index],
        pin: element,
        start: "top top",
        end: "bottom bottom",
        pinSpacing: false,
      }
    });

  })

}