<?php

namespace App\Traits;

use App\Models\Otp;

trait HasOTP
{
    public function getIdentifier()
    {
        return sha1($this->getKey() . $this->email . $this->password);
    }

    /**
     * @param  int  $digits
     * @param  int  $validity
     * @return mixed
     */
    public function generateOtp(int $digits = 6, int $validity = 30): object
    {
        $identifier = $this->getIdentifier();

        Otp::where('identifier', $identifier)->where('is_valid', true)->delete();

        $token = otp()->digits($digits)->generate($identifier);

        Otp::create([
            'identifier' => $identifier,
            'token' => $token,
            'validity' => $validity,
        ]);

        return (object) [
            'status' => true,
            'token' => $token,
            'message' => 'OTP generated',
        ];
    }

    /**
     * @param  string  $token
     * @return object
     */
    public function validateOtp(string $token): object
    {
        $identifier = $this->getIdentifier();

        $otp = Otp::where('identifier', $identifier)->where('token', $token)->first();

        if (blank($otp) && ! $otp->is_valid) {
            return (object) [
                'status' => false,
                'message' => 'OTP is not valid',
            ];
        }

        $validity = $otp->created_at->addMinutes($otp->validity);

        $otp->is_valid = false;
        $otp->save();

        if (now()->greaterThan($validity)) {
            return (object) [
                'status' => false,
                'message' => 'OTP Expired',
            ];
        }

        return (object) [
            'status' => true,
            'message' => 'OTP is valid',
        ];
    }
}
