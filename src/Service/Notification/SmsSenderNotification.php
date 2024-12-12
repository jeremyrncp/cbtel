<?php

namespace App\Service\Notification;

class SmsSenderNotification implements SenderInterface
{
    public function send(string $to, string $template): bool
    {
        $postUrl = "https://sms.capitolemobile.com/api/sendsms/xml";
        //Structure de Données XML
        $xmlString = '<SMS>
            <authentification>
            <username>contact@cbtel.fr</username>
            <password>84e7f4c64647cbabd921044af61326938bd7d58a</password>
            </authentification>
            <message>
            <text>' . $template . '</text>
            <sender>CBTEL</sender>
            </message>
            <recipients>
              <gsm>' . $to . '</gsm>
            </recipients>
        </SMS>';

        $fields = "XML=" . urlencode(utf8_encode($xmlString));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        // Réponse de la requête POST
        $response = curl_exec($ch);
        curl_close($ch);

        if (str_starts_with($response, "SENDING_OK")) {
            return true;
        }

        throw new \Exception($response);
    }
}
