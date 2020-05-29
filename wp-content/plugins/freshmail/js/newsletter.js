import $ from 'jquery';

class Select {
    constructor() {
        this.newsletterBtn = $('.js-newsletter-btn');
        this.newsletterForm =  this.newsletterBtn.closest('.js-newsletter-form');
        this.newsletterEmail = this.newsletterForm.find('.js-newsletter-email');
        this.newsletterAgree = this.newsletterForm.find('.js-newsletter-agree');
        this.newsletterLabel = this.newsletterForm.find('.js-newsletter-label');
        this.newsletterDisplayText = this.newsletterForm.find('.js-newsletter-display-text');
        this.newsletterAgreeChecked = this.newsletterAgree.is(':checked');
        this.newsletterEmailValid = this.newsletterEmail.val();
    }

    events() {
        this.newsletterEmail.on('input', (e)=> {
            this.emailInputIsValid();
            this.btnChangeState();
        });

        this.newsletterAgree.on('click', (e)=> {
            this.agreeCheckboxIsValid();
            this.btnChangeState();
        });

        this.newsletterBtn.on('click', (e) => {
            e.preventDefault();

            this.sendAjax();
        });
    }

    validateEmail(email) {
        let re = /\S+@\S+\.\S+/;
        return re.test(email);
    }

    btnActive() {
        this.newsletterBtn.removeClass('btn--inactive').prop("disabled", false);
    }

    btnInactive() {
        this.newsletterBtn.addClass('btn--inactive').prop("disabled", true);
    }

    emailInputIsValid() {
        this.newsletterEmailValid = this.validateEmail(this.newsletterEmail.val());
    }

    agreeCheckboxIsValid() {
        this.newsletterAgreeChecked = this.newsletterAgree.is(':checked');
    }

    btnChangeState() {
        if (this.newsletterEmailValid && this.newsletterAgreeChecked ) {
            this.btnActive();
        } else {
            this.btnInactive();
        }
    }

    sendAjax() {
        $.ajax({
            url : ajaxurl,
            dataType : "json",
            data : {
                action: "helpuj_newsletter",
                email: this.newsletterEmail.val(),
                agree: this.newsletterAgreeChecked,
                nonce: nonce,
            }
        }).done((data) => {
            switch(data.status) {
                case 'OK':
                    this.displayInfo('Adres email został dodany do newslettera');
                    break;
                case 'ERROR':
                    let errorCode = data.errors[0].code;
                    switch(errorCode) {
                        case 1301:
                            // console.log('Adres email jest niepoprawny');
                            break;
                        case 1302:
                            // console.log('Lista subskrypcyjna nie istnieje lub brak hash\'a listy');
                            break;
                        case 1303:
                            // console.log('Jedno lub więcej pól dodatkowych jest niepoprawne');
                            break;
                        case 1304:
                            this.displayInfo('Subskrybent już istnieje w tej liście subskrypcyjnej', 'error');
                            break;
                        case 1305:
                            // console.log('Próbowano nadać niepoprawny status subskrybenta');
                            break;
                    }
                    break;
            }

        }).fail((data) => {
            // console.log(data);
        });
    }

    displayInfo(text, type = 'success') {
        if (type === 'error') {
            this.newsletterDisplayText.html(text);
            this.newsletterDisplayText.addClass('input__error-text');
            this.newsletterLabel.addClass('label--error');
            this.newsletterEmail.addClass('input--error');
        } else {
            this.newsletterDisplayText.html(text);
            this.newsletterDisplayText.removeClass('input__error-text');
            this.newsletterLabel.removeClass('label--error');
            this.newsletterEmail.removeClass('input--error');
        }
    }

    init() {
        this.events();
    }
}

export default Select;
