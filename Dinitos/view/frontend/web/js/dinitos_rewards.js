require(['jquery', 'loader'], function ($) {
    class RewardManager {
        constructor() {
            this.isRequestInProgress = false;
            this.selectedRewards = [];
            this.availableBalance = 0;
            this.rewardObject = {};
            this.checkoutStep = 2;
            this.init()
        }

        containsObject(arr, obj) {
            return arr.some(item => item.id === obj.id);
        }

        removeObject(arr, obj) {
            return arr.filter(item => item.id !== obj.id);
        }

        addOrRemoveReward(rewardObject) {
            if (!this.containsObject(this.selectedRewards, rewardObject)) {
                if ((this.availableBalance - rewardObject.dinitos_qty) >= 0) {
                    this.selectedRewards.push(rewardObject);
                    this.availableBalance -= rewardObject.dinitos_qty;
                    return true;
                }
            } else {
                this.selectedRewards = this.removeObject(this.selectedRewards, rewardObject);
                this.availableBalance += rewardObject.dinitos_qty;
                return false;
            }
        }

        setRewardsRequest(url) {
            if (this.isRequestInProgress) {
                return;
            }
            this.isRequestInProgress = true;

            let anyRewardSelected = this.selectedRewards.length > 0;

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    rewardsResponse: {
                        'reward_selected': anyRewardSelected,
                        'reward': this.rewardObject
                    }
                },
                success: (response) => {
                    if (response.success === -1) {
                        this.showErrorMessage();
                        window.location.reload();
                    } else {
                        this.reloadCheckoutSummary();
                    }
                },
                error: function (xhr, status, error) {
                },
                complete: () => {
                }
            });
        }

        showErrorMessage() {
            $("#reward-error").toggleClass('is-invisible');
        }

        enableReward(reward) {
            if (reward.hasClass('is-disabled')) {
                reward.removeClass('is-disabled');
                reward.find('.need-dinitos').css('visibility', 'hidden');
            }
        }

        checkEligibility() {
            $('.reward').not('.reward-selected').each((index, element) => {
                if (this.availableBalance < $(element).data('dinitos-qty')) {
                    $(element).addClass('is-disabled');
                } else {
                    $(element).removeClass('is-disabled');
                }
            });
        }

        reloadCheckoutSummary() {
            $(document).trigger("refreshCheckoutSummary", null, null, this.checkoutStep);
            this.isRequestInProgress = false;
        }

        updateBalanceDisplay(display) {
            display.html(this.availableBalance);
        }

        updateRewardsInit() {
            this.availableBalance = $('#balance-display').data('balance-available');

            $('.in-quote').each((index, element) => {
                let id = $(element).data('id');
                let dinitosQty = $(element).data('dinitos-qty');
                let actionUrl = $(element).data('action-url');
                let type = $(element).data('type');

                this.rewardObject = {
                    id: id,
                    dinitos_qty: dinitosQty,
                    action: actionUrl,
                    type: type
                };

                this.addOrRemoveReward(this.rewardObject);
                this.enableReward($(element));
                $(element).addClass('reward-selected')
                $(element).removeClass('in-quote')
            });
            this.updateShortfall()
        }

        selectReward(rewardId) {
            $('#reward-' + rewardId)
                .addClass('reward-selected')
                .find('input[type="checkbox"]').prop('checked', true);
        }

        deselectReward(rewardId) {
            $('#reward-' + rewardId)
                .removeClass('reward-selected')
                .find('input[type="checkbox"]').prop('checked', false);
        }

        handleClick() {
            $('body').on('click', '.reward .rw-btn', (e) => {
                e.stopImmediatePropagation();
                e.preventDefault();

                if (this.isRequestInProgress) {
                    return;
                }

                const $rewardElement = $(e.currentTarget).closest('.reward');

                this.rewardObject = {
                    id: $rewardElement.data('id'),
                    dinitos_qty: $rewardElement.data('dinitos-qty'),
                    action: $rewardElement.data('action-url'),
                    type: $rewardElement.data('type')
                };

                this.addOrRemoveReward(this.rewardObject) ? this.selectReward(this.rewardObject.id) : this.deselectReward(this.rewardObject.id);
                this.updateShortfall()
                this.updateBalanceDisplay($('#balance-display .display-qty'));
                this.checkEligibility();
                this.setRewardsRequest(this.rewardObject.action);
            });
        }

        replaceShortfallQty (string, qty) {
            return string.replace(/\d+/g, qty);
        }

        updateShortfall() {
            $('.reward').each((index, element) => {
                let shortfall = this.availableBalance - $(element).data('dinitos-qty')
                if (shortfall < 0) {
                    let text = $(element).find('.shortfall').html()
                    let qty = Math.abs(shortfall)
                    $(element).find('.shortfall').html(this.replaceShortfallQty(text,qty));
                    $(element).hasClass('reward-selected') ? $(element).find('.shortfall').addClass('shortfall-hidden') : $(element).find('.shortfall').removeClass('shortfall-hidden');
                } else {
                    $(element).find('.shortfall').addClass('shortfall-hidden')
                }
            });
        }

        init() {
            this.updateRewardsInit();
            this.handleClick();
        }
    }

    $(document).ready(() => {
        const rewardManager = new RewardManager();
    });
});