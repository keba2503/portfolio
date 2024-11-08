define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";
    $(document).ready(function () {

        $('.register-table-button').on("click", function () {
            $(".initial.change-after-click").removeClass("hide");
            $(".change-after-click:not(.initial)").addClass("hide");
            $("input[name='rascaCode']").attr('value', $(this).attr("data-number"));
            $(".code-rasca").html($(this).attr("data-number"));
            $(".dialogBox-modal-table-rasca").addClass("is-active");
        })

        $("#register-button").on("click", function (e) {
            e.preventDefault();
            let rasca = $("input[name='rascaCode']").val();
            let customurl = $(".form-rasca.modal-card").data("url")
            let clicked = this;
            let rascaElement = $(clicked).parents('.inside-rascas-content');
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
                            $(".rascas-error.error-message").removeClass("is-invisible");
                            $(".rascas-error.error-message").html(response.message);
                        } else {
                            $(".code-" + rasca).removeClass("no-check");
                            $(".code-" + rasca).addClass("check");
                            $(".code-" + rasca + " .first i").removeClass("icon-pending");
                            $(".code-" + rasca + " .first i").addClass("icon-check");
                            $(".change-after-click").toggleClass("hide");
                            $(".code-" + rasca + " .raffle").toggleClass("hide");
                            $(".code-" + rasca + " .initial-without-raffle").remove();
                        }
                    }
                    else {
                        if (response.hasOwnProperty("redirectUrl")) {
                            location.href = response.redirectUrl;
                        }
                    }
                },
                error: function (errorThrown) {
                    $(".rascas-error.error-message").removeClass("is-invisible");
                    $(".rascas-error.error-message").html(errorThrown);
                },
                complete: function () {
                    $().disableLoader(rascaElement);
                }
            });
        });
        loadMyPagination();
    });

    //creating an array for adding numbers in a page
    var countList = new Array();
    var countList2 = new Array();
    //creating an array for adding number of pagess
    var addPageList = new Array();
    var presentPage = 1;
    var countPerEachPage = 12;
    var countOfPages = 0;
    var list = document.getElementById("countList");
    var select;
    var text = `<div class="anniversary-codes-table__no-codes">
                <p class="no-codes">No hay participaciones asociadas a este filtro</p>
            </div>`;

    //function for adding how many numbers in total
    function prepareList() {
        for (let count = 0; count < list.children.length; count++) {
            countList.push(document.querySelector('#countList > div:nth-child(' + (count + 1) + ')'));
        }
        countOfPages = getCountOfPages();
    }

    //function for creating how many how many number per each page
    function getCountOfPages() {
        return Math.ceil(countList.length / countPerEachPage);
    }

    document.getElementById("previous").onclick = getPreviousPage;
    document.getElementById("next").onclick = getNextPage;

    //function for moving to next page
    function getNextPage() {
        presentPage += 1;
        loadMyPaginationList();
    }

    //function for moving previous page
    function getPreviousPage() {
        presentPage -= 1;
        loadMyPaginationList();
    }

    //function for moving to first page
    function getFirstPage() {
        presentPage = 1;
        loadMyPaginationList();
    }

    //function for creating how to move between the pages
    function loadMyPaginationList() {
        var start = ((presentPage - 1) * countPerEachPage);
        var end = start + countPerEachPage;
        addPageList = countList.slice(start, end);
        createPageList();
        validatePageCount();
        hidePage();
    }

    //function for adding numbers to each page
    function createPageList() {
        list.innerHTML = "";
        list.append(...addPageList)
    }

    //function for validating real time condition like if move to last page, last page disabled etc
    function validatePageCount() {
        if (presentPage == 1) {
            document.getElementById("previous").disabled = true;
            document.getElementById("previous").classList.add("disabled");
        } else {
            document.getElementById("previous").disabled = false;
            document.getElementById("previous").classList.remove("disabled");
        }
        if (presentPage >= countOfPages) {
            document.getElementById("next").disabled = true;
            document.getElementById("next").classList.add("disabled");
        } else {
            document.getElementById("next").disabled = false;
            document.getElementById("next").classList.remove("disabled");
        }
    }

    function hidePage() {
        if (countOfPages <= 1) {
            document.querySelector('.pages-input').classList.add("hide");
        } else {
            document.querySelector('.pages-input').classList.remove("hide");
        }
    }

    //function for loading pagination functionality
    function loadMyPagination() {
        prepareList();
        loadMyPaginationList();
        countList2 = countList;
    }

    function updateCards() {
        countOfPages = getCountOfPages();
        loadMyPaginationList();
        getFirstPage()
        validatePageCount();
    }

    function noCards(number) {
        if (number == 0) {
            document.querySelector('#countList').classList.add("no-grid");
            list.innerHTML += text;
            document.querySelector('.pages-input').classList.add("hide");
        } else {
            document.querySelector('#countList').classList.remove("no-grid")
            document.querySelector('.pages-input').classList.remove("hide");
        }
    }

    document.getElementById("filterRascas").onchange = val;

    function val() {
        select = document.getElementById("filterRascas").value;
        switch (select) {
            case '0':
                countList = countList2;
                updateCards();
                noCards(countList.length);
                hidePage();
                break;
            case '1':
                countList = countList2.filter(element => element.classList.contains('win-redeemed'))
                updateCards();
                noCards(countList.length);
                hidePage();
                break;
            case '2':
                countList = countList2.filter(element => element.classList.contains('win-no-redeemed'))
                updateCards();
                noCards(countList.length);
                hidePage();
                break;
            case '3':
                countList = countList2.filter(element => element.classList.contains('no-win'))
                updateCards();
                noCards(countList.length);
                hidePage();
                break;
            case '4':
                countList = countList2.filter(element => element.classList.contains('no-check'))
                updateCards();
                noCards(countList.length);
                hidePage();
                break;
            case '5':
                countList = countList2.filter(element => element.classList.contains('check'))
                updateCards();
                noCards(countList.length);
                hidePage();
                break;
            default:
                break;
        }
    }
});