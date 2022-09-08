<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\GoogleSpreadsheets;

use Google\Client;
use Google\Exception;
use Google\Service\Sheets;
use LogicException;

final class SheetsClientFactory
{
    public function __invoke(
        string $projectId,
        string $privateKeyId,
        string $privateKey,
        string $clientName,
        string $clientId
    ): Sheets {
        $client = new Client();
        $jsonKey = [
            "type" => "service_account",
            "project_id" => $projectId,
            "private_key_id" => $privateKeyId,
            "private_key" => $privateKey,
            "client_email" => "$clientName@$projectId.iam.gserviceaccount.com",
            "client_id" => $clientId,
            "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
            "token_uri" => "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/$clientName%40$projectId.iam.gserviceaccount.com",
        ];
        try {
            $client->setAuthConfig($jsonKey);
        } catch (Exception $e) {
            throw new LogicException("Invalid google client configuration", 0, $e);
        }
        $client->addScope(Sheets::SPREADSHEETS);
        $client->setApplicationName("Want2watch");

        return new Sheets($client);
    }
}
