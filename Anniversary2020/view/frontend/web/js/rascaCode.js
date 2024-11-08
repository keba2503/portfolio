define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";
    $(document).ready(function () {

        $(".form-rasca.rasca-action .register-table-button").on("click", function (e) {
            e.preventDefault();
            $(".rascas-error-2.error-message").addClass("d-none");
            let rasca = $(this).data("rasca");
            let customurl = $(this).data("url")
            let clicked = this;
            let rascaElement = $(clicked).parents('.form-rasca.rasca-action');
            $().activeLoader(rascaElement);
            $.ajax({
                url: customurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    rascaCode: rasca
                },
                success: function (response) {
                    if (response.hasOwnProperty("isError")) {
                        if (response.isError) {
                            $(".rascas-error-2.error-message").removeClass("d-none");
                            $(".rascas-error-2.error-message").html(response.message);
                        } else {
                            $(".rasca-" + rasca + " .raffle").toggleClass("hide");
                            $(".rasca-" + rasca + " .form-rasca.rasca-action").remove();
                        }
                    }
                    else {
                        if (response.hasOwnProperty("redirectUrl")) {
                            location.href = response.redirectUrl;
                        }
                    }
                },
                error: function (errorThrown) {
                    $(".rascas-error-2.error-message").removeClass("d-none");
                    $(".rascas-error-2.error-message").html(errorThrown);
                },
                complete: function () {
                    $().disableLoader(rascaElement);
                }
            });
        });

        $(".rascar").on("click", function (e) {
            e.preventDefault();
            $(".rascas-error.error-message").addClass("d-none");
            let rasca = $(this).data("rasca");
            let customurl = $(this).data("url")
            let clicked = this;
            let rascaElement = $(clicked).parents('.images-rascas');
            $().activeLoader(rascaElement);
            $.ajax({
                url: customurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    rascaCode: rasca
                },
                success: function (response) {
                    if (response.hasOwnProperty("isError")) {
                        if (response.isError) {
                            $(".rascas-error.error-message").removeClass("d-none");
                            $(".rascas-error.error-message").html(response.message);
                        } else {
                            let index = $(clicked).closest('.gallery-item').attr('data-index');
                            $('.content-text-rascas > div:nth(' + index + ')').removeClass('hidden no-show');
                            $(clicked).addClass("hidden");
                            setTimeout(function () {
                                $('.content-text-rascas > div:nth(' + index + ')').attr('data-rasca', '1');
                                $('.gallery-container > div:nth(' + index + ')').attr('data-rasca', '1');
                                $(clicked).remove();
                            }, 3000);
                            setTimeout(function () {
                                if ($(clicked).data("prize")) {
                                    $(".dialogBox-modal-dorasca").addClass("is-active");
                                    $("#prize-text").html($(clicked).data("prize"))
                                }
                            }, 2000);
                        }
                    } else {
                        if (response.hasOwnProperty("redirectUrl")) {
                            location.href = response.redirectUrl;
                        }
                    }
                },
                error: function (xhr, status, errorThrown) {
                    $(".rascas-error.error-message").removeClass("d-none");
                    $(".rascas-error.error-message").html(errorThrown);
                },
                complete: function () {
                    $().disableLoader(rascaElement);
                }
            });
        });

        // Carrusel
        const galleryContainer = document.querySelector('.gallery-container');
        const galleryControlsContainer = document.querySelector('.gallery-controls');
        const galleryControls = ['anterior', 'siguiente'];
        const galleryItems = document.querySelectorAll('.gallery-item');
        const contentItems = document.querySelectorAll('.inside-rascas-content');

        class Carousel {
            constructor(container, items, controls, rascas) {
                this.carouselContainer = container;
                this.carouselControls = controls;
                this.carouselArray = [...items];
                this.rascasArray = [...rascas];
            }

            // Update css classes for gallery
            updateGallery() {
                $(".rascas-error.error-message").addClass("d-none");
                $(".rascas-error-2.error-message").addClass("d-none");
                this.carouselArray.forEach(element => {
                    element.classList.remove('gallery-item-1');
                    element.classList.remove('gallery-item-2');
                    element.classList.remove('gallery-item-3');
                    element.classList.remove('active');
                    if (element.querySelector(".rascar")) {
                        element.querySelector(".rascar").setAttribute('disabled', '');
                    }
                });
                this.rascasArray.forEach(element => {
                    element.classList.add('hidden');
                });
                this.carouselArray.slice(0, 2).forEach((element, i) => {
                    if (i == 0) {
                        element.classList.add(`active`);
                        if (element.querySelector(".rascar")) {
                            element.querySelector(".rascar").removeAttribute('disabled');
                        }
                    }
                    element.classList.add(`gallery-item-${i + 1}`);
                });

                this.rascasArray[0].classList.remove(`hidden`);

                this.carouselArray.slice(this.carouselArray.length - 1, this.carouselArray.length).forEach((el, i) => {
                    el.classList.add(`gallery-item-${i + 3}`);
                });
            }

            // Update the current order of the carouselArray and gallery
            setCurrentState(direction) {

                if (direction.className == 'gallery-controls-anterior') {
                    this.carouselArray.unshift(this.carouselArray.pop());
                    this.rascasArray.unshift(this.rascasArray.pop());
                    if (parseInt(document.querySelector(".actual-rasca").innerHTML) == 1) {
                        document.querySelector(".actual-rasca").innerHTML = document.querySelector(".max-number-rasca").innerHTML
                    } else {
                        document.querySelector(".actual-rasca").innerHTML = (parseInt(document.querySelector(".actual-rasca").innerHTML) - 1)
                    }

                } else {
                    this.carouselArray.push(this.carouselArray.shift());
                    this.rascasArray.push(this.rascasArray.shift());

                    if (parseInt(document.querySelector(".actual-rasca").innerHTML) == parseInt(document.querySelector(".max-number-rasca").innerHTML)) {
                        document.querySelector(".actual-rasca").innerHTML = 1
                    } else {
                        document.querySelector(".actual-rasca").innerHTML = (parseInt(document.querySelector(".actual-rasca").innerHTML) + 1)
                    }
                }

                this.updateGallery();
            }

            // Add a click event listener to trigger setCurrentState method to rearrange carousel
            useControls() {
                const triggers = [...galleryControlsContainer.childNodes];

                triggers.forEach(control => {
                    control.addEventListener('click', e => {
                        e.preventDefault();

                        this.setCurrentState(control);

                    });
                });
            }
        }

        const rascaCarousel = new Carousel(galleryContainer, galleryItems, galleryControls, contentItems);

        rascaCarousel.useControls();
    });
});