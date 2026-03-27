<?php
namespace Core;

/**
 * BrevoMailer - Envoi d'emails via l'API Brevo (ex-Sendinblue)
 * Utilise l'API REST Brevo directement (pas de lib externe)
 */
class BrevoMailer
{
    private static $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        $apiKey    = Env::get('BREVO_API_KEY');
        $fromEmail = Env::get('MAIL_FROM', 'contact@marketflow.fr');
        $fromName  = Env::get('MAIL_FROM_NAME', 'MarketFlow');
        $replyTo   = Env::get('MAIL_REPLY_TO', $fromEmail);

        if (!$apiKey) {
            error_log('[BrevoMailer] BREVO_API_KEY manquante');
            return false;
        }

        $payload = json_encode([
            'sender'      => ['name' => $fromName, 'email' => $fromEmail],
            'to'          => [['email' => $toEmail, 'name' => $toName]],
            'replyTo'     => ['email' => $replyTo],   // fix #4
            'subject'     => $subject,
            'htmlContent' => $htmlBody,
        ]);

        $ch = curl_init(self::$apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 10,              // fix #1
            CURLOPT_CONNECTTIMEOUT => 5,               // fix #1
            CURLOPT_HTTPHEADER     => [
                'accept: application/json',
                'api-key: ' . $apiKey,
                'content-type: application/json',
            ],
        ]);

        $response = curl_exec($ch);

        // fix #2 — détection erreur cURL
        if ($response === false) {
            error_log('[BrevoMailer] cURL error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 201) {
            return true;
        }

        error_log('[BrevoMailer] Erreur ' . $httpCode . ' : ' . $response);
        return false;
    }

    public static function sendWelcome(string $email, string $name): bool
    {
        $subject = "Bienvenue sur MarketFlow, {$name} ! 🎉";
        return self::send($email, $name, $subject, self::templateWelcome($name));
    }

    public static function sendOrderConfirmation(string $email, string $name, string $orderNumber, float $total, array $items): bool
    {
        $subject = "Confirmation de commande #{$orderNumber} ✅";
        return self::send($email, $name, $subject, self::templateOrderConfirmation($name, $orderNumber, $total, $items));
    }

    // ================================================================
    // TEMPLATES HTML
    // ================================================================

    private static function templateWelcome(string $name): string
    {
        $appUrl = Env::get('APP_URL', 'https://www.marketflow.fr');
        return "
        <!DOCTYPE html>
        <html><head><meta charset='UTF-8'></head>
        <body style='font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px'>
          <div style='max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden'>
            <div style='background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:40px;text-align:center'>
              <h1 style='color:#fff;margin:0;font-size:28px'>Bienvenue sur MarketFlow 🎉</h1>
            </div>
            <div style='padding:40px'>
              <p style='font-size:18px'>Bonjour <strong>" . htmlspecialchars($name) . "</strong>,</p>
              <p>Ton compte est créé ! Tu peux dès maintenant explorer des milliers de produits digitaux.</p>
              <div style='text-align:center;margin:30px 0'>
                <a href='{$appUrl}/products' style='background:#6366f1;color:#fff;padding:14px 30px;border-radius:8px;text-decoration:none;font-weight:bold'>
                  Découvrir les produits →
                </a>
              </div>
              <p style='color:#888;font-size:14px'>Si tu as des questions, réponds à cet email.</p>
            </div>
            <div style='background:#f8f8f8;padding:20px;text-align:center;color:#aaa;font-size:12px'>
              MarketFlow · <a href='{$appUrl}' style='color:#6366f1'>marketflow.fr</a>
            </div>
          </div>
        </body></html>";
    }

    private static function templateOrderConfirmation(string $name, string $orderNumber, float $total, array $items): string
    {
        $appUrl = Env::get('APP_URL', 'https://www.marketflow.fr');

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= "
            <tr>
              <td style='padding:12px;border-bottom:1px solid #eee'>" . htmlspecialchars($item['title']) . "</td>
              <td style='padding:12px;border-bottom:1px solid #eee;text-align:right'>" . number_format($item['price'], 2, ',', ' ') . " €</td>
            </tr>";
        }

        return "
        <!DOCTYPE html>
        <html><head><meta charset='UTF-8'></head>
        <body style='font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px'>
          <div style='max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden'>
            <div style='background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:40px;text-align:center'>
              <h1 style='color:#fff;margin:0'>Commande confirmée ✅</h1>
              <p style='color:rgba(255,255,255,0.85);margin:8px 0 0'>#{$orderNumber}</p>
            </div>
            <div style='padding:40px'>
              <p>Bonjour <strong>" . htmlspecialchars($name) . "</strong>,</p>
              <p>Ta commande a bien été reçue et ton paiement est confirmé.</p>
              <table style='width:100%;border-collapse:collapse;margin:20px 0'>
                <thead>
                  <tr style='background:#f8f8f8'>
                    <th style='padding:12px;text-align:left'>Produit</th>
                    <th style='padding:12px;text-align:right'>Prix</th>
                  </tr>
                </thead>
                <tbody>{$itemsHtml}</tbody>
                <tfoot>
                  <tr>
                    <td style='padding:12px;font-weight:bold'>Total</td>
                    <td style='padding:12px;font-weight:bold;text-align:right'>" . number_format($total, 2, ',', ' ') . " €</td>
                  </tr>
                </tfoot>
              </table>
              <div style='text-align:center;margin:30px 0'>
                <a href='{$appUrl}/orders' style='background:#6366f1;color:#fff;padding:14px 30px;border-radius:8px;text-decoration:none;font-weight:bold'>
                  Voir mes commandes →
                </a>
              </div>
            </div>
            <div style='background:#f8f8f8;padding:20px;text-align:center;color:#aaa;font-size:12px'>
              MarketFlow · <a href='{$appUrl}' style='color:#6366f1'>marketflow.fr</a>
            </div>
          </div>
        </body></html>";
    }
}
