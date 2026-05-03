<?php
/**
 * Service: Notificações por Email
 */

require_once __DIR__ . '/../config/app.php';

class EmailService
{
    /**
     * Enviar email de fatura ao cliente
     */
    public static function sendInvoice(array $invoice, array $company): bool
    {
        $to      = $invoice['client_email'];
        $subject = "Fatura {$invoice['invoice_number']} - {$company['name']}";
        
        $body = self::getTemplate('invoice', [
            'company_name'   => $company['name'],
            'client_name'    => $invoice['client_name'],
            'invoice_number' => $invoice['invoice_number'],
            'due_date'       => formatDate($invoice['due_date']),
            'total'          => formatMoney((float)$invoice['total']),
            'link'           => APP_URL . "/faturas.php?id=" . $invoice['id'],
            'bank_info'      => $company['bank_name'] . " - " . $company['bank_account']
        ]);

        return self::send($to, $subject, $body);
    }

    /**
     * Enviar email genérico (HTML)
     */
    private static function send(string $to, string $subject, string $htmlBody): bool
    {
        if (empty($to)) return false;

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: ' . APP_NAME . ' <noreply@faturamz.pro>',
            'X-Mailer: PHP/' . phpversion()
        ];

        // LOG para depuração (já que mail() pode falhar em local)
        $logPath = __DIR__ . '/../logs/emails.log';
        if (!is_dir(dirname($logPath))) mkdir(dirname($logPath), 0777, true);
        $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: $to | SUBJECT: $subject\n";
        file_put_contents($logPath, $logEntry, FILE_APPEND);

        return @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
    }

    /**
     * Templates HTML profissionais
     */
    private static function getTemplate(string $type, array $data): string
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: sans-serif; line-height: 1.6; color: #334155; }
                .container { max-width: 600px; margin: 0 auto; padding: 40px 20px; border: 1px solid #e2e8f0; border-radius: 12px; }
                .header { text-align: center; margin-bottom: 30px; }
                .btn { display: inline-block; padding: 12px 24px; background: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; }
                .footer { margin-top: 40px; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; pt: 20px; }
                .total { font-size: 24px; font-weight: 800; color: #0f172a; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2 style="margin:0; color:#1e293b;"><?= $data['company_name'] ?></h2>
                </div>
                
                <p>Olá, <strong><?= $data['client_name'] ?></strong>,</p>
                <p>Esperamos que esteja bem. Informamos que a sua fatura <strong><?= $data['invoice_number'] ?></strong> já está disponível para consulta e pagamento.</p>
                
                <div style="background:#f8fafc; padding:20px; border-radius:12px; text-align:center; margin:20px 0;">
                    <p style="margin:0; font-size:12px; text-transform:uppercase; font-weight:bold; color:#64748b;">Valor Total</p>
                    <p class="total"><?= $data['total'] ?></p>
                    <p style="margin:0; font-size:12px; color:#ef4444;">Vence em <?= $data['due_date'] ?></p>
                </div>

                <p>Pode visualizar os detalhes completos e baixar o PDF clicando no botão abaixo:</p>
                <div style="text-align:center;">
                    <a href="<?= $data['link'] ?>" class="btn">Visualizar Fatura Online</a>
                </div>

                <div style="margin-top:30px; padding:15px; border-left:4px solid #3b82f6; background:#eff6ff;">
                    <p style="margin:0; font-size:13px;"><strong>Dados para Pagamento:</strong><br><?= $data['bank_info'] ?></p>
                </div>

                <div class="footer">
                    <p>Este é um email automático, por favor não responda diretamente.<br>
                    &copy; <?= date('Y') ?> <?= $data['company_name'] ?>. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
