<?php

namespace Clarkeash\Doorman\Drivers;

use Illuminate\Support\Str;

class BasicDriver implements DriverInterface
{

    /**
     * Create an invite code.
     *
     * @return string
     */
    public function code(): string
    {
        $alphabets = explode(',','2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,P,Q,R,S,T,U,V,W,X,Y,Z');
        $randomStr = Str::random(config('doorman.basic.length', 10));
        $randomStr = Str::replaceArray('I',$alphabets,$randomStr);
        $randomStr = Str::replaceArray('1',$alphabets,$randomStr);
        $randomStr = Str::replaceArray('i',$alphabets,$randomStr);
        $randomStr = Str::replaceArray('0',$alphabets,$randomStr);
        $randomStr = Str::replaceArray('O',$alphabets,$randomStr);
        $randomStr = Str::replaceArray('o',$alphabets,$randomStr);
        return $randomStr;
    }
}
