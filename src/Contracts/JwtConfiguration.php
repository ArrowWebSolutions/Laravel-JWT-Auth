<?php
namespace Arrow\JwtAuth\Contracts;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Validator;
use Lcobucci\JWT\Signer\Key;

interface JwtConfiguration
{
    public function parser(): Parser;
    public function signer(): Signer;
    public function validator(): Validator;
    public function signingKey(): Key;
    public function verificationKey(): Key;
}
