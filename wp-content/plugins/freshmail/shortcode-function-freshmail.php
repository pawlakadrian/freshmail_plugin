<?php
function shortcode_freshmail() {
    return
        '<form id="newsletter-form" class="newsletter__column newsletter__form js-newsletter-form">
                <div class="newsletter__form-header">
                    <label class="newsletter__form-label label js-newsletter-label">
                        Twój e-mail
                        <input type="email" class="input js-newsletter-email" placeholder="Twój e-mail">
                    </label>
                    <button class="newsletter__btn btn btn--primary btn--inactive js-newsletter-btn" type="submit" >
                        Zapisz
                    </button>
                    <span class="newsletter__info-text js-newsletter-display-text"></span>
                    <br><label class="newsletter__form-label-checkbox checkbox__label">
                        <input class="checkbox js-newsletter-agree" type="checkbox">
                        <span class="checkbox__new newsletter__checkbox-new">
        </span>
                        <div class="checkbox__label-text">
                            Wyrażam zgodę na przesyłanie na podany przy subkoncie adres e-mail wiadomości zawierających informacje marketingowe i promocyjne w formie Newslettera obejmującego m.in. treści edukacyjne oraz informacje o nowościach w serwisie.
                        </div>
                    </label>
                </div>
            </form>';
}