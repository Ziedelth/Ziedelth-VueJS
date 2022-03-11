<?php

class EmailTemplate
{
    /**
     * @param string $pseudo
     * @param string $hash
     * @return string
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
}