(function ($) {
	'use strict';

	// Spinner
	const spinner = function () {
		setTimeout(function () {
			if ($('#spinner').length > 0) {
				$('#spinner').removeClass('show');
			}
		}, 1);
	};
	spinner(0);

	// Initiate the wowjs
	try {
		new WOW().init();
	} catch (e) {}

	// Carga imagen modal (solo si existe #imageModal en la página)
	try {
		const imageModal = document.getElementById('imageModal');
		if (imageModal) {
			imageModal.addEventListener('show.bs.modal', function (event) {
				const button = event.relatedTarget;
				const imgSource = button.getAttribute('src');
				const modalImg = imageModal.querySelector('#imagePreview');
				if (modalImg) {
					modalImg.src = imgSource;
				}
			});
		}
	} catch (e) {}

	// Ir arriba
	$(document).ready(function () {
		$('.ir-arriba').click(function () {
			$('body, html').animate({ scrollTop: '0px' }, 300);
		});
		$(window).scroll(function () {
			if ($(this).scrollTop() > 0) {
				$('.ir-arriba').slideDown(300);
			} else {
				$('.ir-arriba').slideUp(300);
			}
		});
	});

	// Acordeon
	try {
		const questions = document.querySelectorAll('.question');
		questions.forEach(function (question) {
			if (question.dataset.accordionInit) {
				return;
			}
			const btn = question.querySelector('.question-btn');
			if (!btn) {
				return;
			}
			btn.addEventListener('click', function () {
				questions.forEach(function (item) {
					if (item !== question) {
						item.classList.remove('show-text');
					}
				});
				question.classList.toggle('show-text');
			});
			question.dataset.accordionInit = '1';
		});
	} catch (e) {}

	// Back to top button
	$(window).scroll(function () {
		if ($(this).scrollTop() > 300) {
			$('.back-to-top').fadeIn('slow');
		} else {
			$('.back-to-top').fadeOut('slow');
		}
	});
	$('.back-to-top').click(function () {
		$('html, body').animate({ scrollTop: 0 }, 1500, 'easeInOutExpo');
		return false;
	});

	// Testimonial carousel
	try {
		$('.testimonial-carousel').owlCarousel({
			autoplay: true,
			smartSpeed: 1000,
			autoplayTimeout: 2500,
			center: true,
			dots: true,
			loop: true,
			margin: 50,
			responsiveClass: true,
			responsive: {
				0: { items: 1 },
				576: { items: 1 },
				768: { items: 2 },
				992: { items: 2 },
				1200: { items: 3 },
			},
		});
	} catch (e) {}

	// Noticias carousel
	try {
		$('.notice-carousel').owlCarousel({
			autoplay: true,
			smartSpeed: 1000,
			autoplayTimeout: 5000,
			center: true,
			dots: true,
			loop: true,
			margin: 50,
			responsiveClass: true,
			responsive: {
				0: { items: 1 },
				576: { items: 1 },
				768: { items: 2 },
				992: { items: 2 },
				1200: { items: 3 },
			},
		});
	} catch (e) {}

	// Modal Video
	$(document).ready(function () {
		let $videoSrc;
		$('.btn-play').click(function () {
			$videoSrc = $(this).data('src');
		});
		$('#videoModal').on('shown.bs.modal', function () {
			if ($videoSrc) {
				$('#video').attr(
					'src',
					$videoSrc + '?autoplay=1&modestbranding=1&showinfo=0'
				);
			}
		});
		$('#videoModal').on('hide.bs.modal', function () {
			if ($videoSrc) {
				$('#video').attr('src', $videoSrc);
			}
		});
	});
})(jQuery);
