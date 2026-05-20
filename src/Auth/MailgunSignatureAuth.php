<?php

namespace STS\EmailEvents\Auth;

use Illuminate\Http\Request;

/**
 * Verifies Mailgun webhook signatures.
 *
 * This implements Mailgun's HMAC-SHA256 scheme (timestamp + token signed
 * with a shared secret) and is specific to Mailgun. Other providers use
 * different schemes — e.g. SendGrid signs event webhooks with ECDSA
 * public-key verification — so this authorizer should not be wired to a
 * non-Mailgun provider.
 */
class MailgunSignatureAuth
{
    /** @var string */
    protected $signatureKey;

    /**
     * @param $signatureKey
     */
    public function __construct( $signatureKey )
    {
        $this->signatureKey = $signatureKey;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function __invoke( Request $request )
    {
        if (abs(time() - $request->input('signature.timestamp')) > 15) {
            return false;
        }

        return $this->buildSignature($request) === $request->input('signature.signature');
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function buildSignature( Request $request )
    {
        return hash_hmac(
            'sha256',
            $request->input('signature.timestamp') . $request->input('signature.token'),
            $this->signatureKey
        );
    }
}
