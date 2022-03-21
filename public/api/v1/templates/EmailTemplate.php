<?php

class EmailTemplate
{
    /**
     * This function returns a string containing the HTML code of the email template for the registration confirmation
     * email
     *
     * @param string $pseudo The pseudo of the user
     * @param string $hash The hash of the user's email.
     *
     * @return string A string containing the email template.
     */
    static function getEmailRegisterTemplate(string $pseudo, string $hash): string
    {
        return '<div style="margin: 0;">
    <div style="display: flex">
        <img src="https://ziedelth.fr/images/favicon.jpg" style="width: 64px; border-radius: 8px" alt="Icon">

        <div style="margin-left: 0.5rem">
            <p style="margin-bottom: 0; font-weight: bold">' . $pseudo . ',</p>
            <p style="margin-top: 0">Merci de votre inscription</p>
        </div>
    </div>

    <div style="margin-top: 1vh">
        <p style="margin-top: 0; margin-bottom: 10px">Veuillez cliquez sur le lien suivant pour terminer votre inscription :</p>
        <a href="https://ziedelth.fr/a/' . $hash . '" style="text-decoration: underline; text-decoration-color: black; color: black">Confirmer mon inscription</a>

        <p style="margin-bottom: 0">Votre inscription ne sera effective que si vous cliquez sur le lien de confirmation ci-dessus.</p>
        <i>Vous ne pourrez vous connecter que lorsque votre adresse mail sera confirmée.</i>

        <p style="margin-bottom: 0">Cordialement,</p>
        <p style="margin-top: 0">Ziedelth.fr</p>
        <div>
            <i>Si vous n\'êtes pas à l\'origine de cette demande, vous pouvez ignorer ce mail.</i>
            <br>
            <i>Cette action n\'est valable que 10 minutes.</i>
        </div>
        
        <div style="margin-top: 0.5vh"><i>Ce mail est envoyé automatiquement, merci de ne pas y répondre.</i></div>
    </div>
</div>';
    }

    /**
     * Returns a string containing the HTML code of the account deletion template
     *
     * @param string pseudo The pseudo of the account that will be deleted
     * @param string hash The hash of the account to delete
     *
     * @return string A string.
     */
    static function getAccountDeletedTemplate(string $pseudo, string $hash): string
    {
        return '<div style="margin: 0;">
    <div style="display: flex">
        <img src="https://ziedelth.fr/images/favicon.jpg" style="width: 64px; border-radius: 8px" alt="Icon">

        <div style="margin-left: 0.5rem">
            <p style="margin-bottom: 0; font-weight: bold">' . $pseudo . ',</p>
            <p style="margin-top: 0">Une demande de suppression de compte a été effectuée</p>
        </div>
    </div>

    <div style="margin-top: 1vh">
        <p style="margin-top: 0; margin-bottom: 10px">Veuillez cliquez sur le lien suivant pour supprimer définitivement votre compte :</p>
        <a href="https://ziedelth.fr/a/' . $hash . '" style="text-decoration: underline; text-decoration-color: black; color: black">Confirmer la suppression de mon compte</a>

        <p style="margin-bottom: 0">Votre demande de suppression de compte ne sera effective que si vous cliquez sur le lien de confirmation ci-dessus.</p>
        <i>Une fois supprimé, toutes vos données ne pourront pas être récupérées.</i>

        <p style="margin-bottom: 0">Cordialement,</p>
        <p style="margin-top: 0">Ziedelth.fr</p>
        <div>
            <i>Si vous n\'êtes pas à l\'origine de cette demande, vous pouvez ignorer ce mail.</i>
            <br>
            <i>Cette action n\'est valable que 10 minutes.</i>
        </div>
        
        <div style="margin-top: 0.5vh"><i>Ce mail est envoyé automatiquement, merci de ne pas y répondre.</i></div>
    </div>
</div>';
    }

    static function getPasswordResetTemplate(string $pseudo, string $hash): string
    {
        return '<div style="margin: 0;">
    <div style="display: flex">
        <img src="https://ziedelth.fr/images/favicon.jpg" style="width: 64px; border-radius: 8px" alt="Icon">

        <div style="margin-left: 0.5rem">
            <p style="margin-bottom: 0; font-weight: bold">' . $pseudo . ',</p>
            <p style="margin-top: 0">Une demande de changement de mot de passe a été effectuée</p>
        </div>
    </div>

    <div style="margin-top: 1vh">
        <p style="margin-top: 0; margin-bottom: 10px">Veuillez cliquez sur le lien suivant pour changer votre mot de passe :</p>
        <a href="https://ziedelth.fr/a/' . $hash . '" style="text-decoration: underline; text-decoration-color: black; color: black">Changer mon mot de passe</a>

        <p style="margin-bottom: 0">Votre demande de changement de mot de passe ne sera effective que si vous cliquez sur le lien de confirmation ci-dessus.</p>

        <p style="margin-bottom: 0">Cordialement,</p>
        <p style="margin-top: 0">Ziedelth.fr</p>
        <div>
            <i>Si vous n\'êtes pas à l\'origine de cette demande, vous pouvez ignorer ce mail.</i>
            <br>
            <i>Cette action n\'est valable que 10 minutes.</i>
        </div>
        
        <div style="margin-top: 0.5vh"><i>Ce mail est envoyé automatiquement, merci de ne pas y répondre.</i></div>
    </div>
</div>';
    }

    static function getConfirmationPasswordResetTemplate(string $pseudo): string
    {
        return '<div style="margin: 0;">
    <div style="display: flex">
        <img src="https://ziedelth.fr/images/favicon.jpg" style="width: 64px; border-radius: 8px" alt="Icon">

        <div style="margin-left: 0.5rem">
            <p style="margin-bottom: 0; font-weight: bold">' . $pseudo . ',</p>
            <p style="margin-top: 0">Votre mot de passe a bien été changé</p>
        </div>
    </div>

    <div style="margin-top: 1vh">
        <p>Nous vous confirmons que votre mot de passe a bien été changé</p>

        <p style="margin-bottom: 0">Cordialement,</p>
        <p style="margin-top: 0">Ziedelth.fr</p>
        <div>
            <i>Si vous n\'êtes pas à l\'origine de cette demande, veuillez contacter le support à contact@ziedelth.fr</i>
        </div>
        
        <div style="margin-top: 0.5vh"><i>Ce mail est envoyé automatiquement, merci de ne pas y répondre.</i></div>
    </div>
</div>';
    }
}