<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Request;

class ReCaptchaValidator
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function captchaVerify(Request $request)
    {
        $recaptcha = $request->get('g-recaptcha-response');
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            "secret" => "6Ld9-f4UAAAAALopnvCMVDtIs11WxevF0TtuRuOB", "response" => $recaptcha));
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);

        return $data->success;
    }
}