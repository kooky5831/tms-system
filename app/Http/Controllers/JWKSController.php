<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class JWKSController extends Controller
{
    public function serve()
    {
        $privateKeyPath = storage_path('key/private.pem');

        if (!file_exists($privateKeyPath)) {
            return response()->json(['error' => 'Private key not found'], 500);
        }

        $privateKey = file_get_contents($privateKeyPath);
        $res = openssl_pkey_get_private($privateKey);

        if (!$res) {
            $error = openssl_error_string();
            return response()->json(['error' => 'Failed to load private key: ' . $error], 500);
        }
        
        $keyDetails = openssl_pkey_get_details($res);
        $publicKeyPem = $keyDetails['key'];

        // Convert to JWK using WebToken library
        $jwk = $this->convertPemToJwk($publicKeyPem);

        return response()->json(['keys' => [$jwk]]);
    }

    // Helper to convert PEM to JWK
    private function convertPemToJwk($publicKeyPem)
    {
        $details = openssl_pkey_get_details(openssl_pkey_get_public($publicKeyPem));
        $rsa = $details['rsa'];
        $keyIdHash = hash('sha256', 'mUll5MrSUTZ4nZa8sudADBprk3I1FnNs', true);
        $keyId = rtrim(strtr(base64_encode($keyIdHash), '+/', '-_'), '=');

        return [
            'kty' => 'RSA',
            'e' => $this->base64url_encode($rsa['e']),
            'n' => $this->base64url_encode($rsa['n']),
            'use' => 'sig',
            'alg' => 'RS256',
            'kid' => $keyId,
        ];
    }

    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
