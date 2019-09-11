<?php

namespace Clarkeash\Doorman;

use Carbon\Carbon;
use Clarkeash\Doorman\Exceptions\DuplicateException;
use Clarkeash\Doorman\Models\BaseInvite;
use Illuminate\Support\Str;

class Generator
{
    protected $amount = 1;
    protected $uses = 1;
    protected $issuedTo = null;
    protected $email = null;
    protected $expiry;

    protected $paymentMode = null;
    protected $paymentTxnid = null;
    protected $paymentAmount = null;

    /**
     * @var \Clarkeash\Doorman\DoormanManager
     */
    protected $manager;

    /**
     * @var BaseInvite
     */
    protected $invite;

    public function __construct(DoormanManager $manager, BaseInvite $invite)
    {
        $this->manager = $manager;
        $this->invite = $invite;
    }

    /**
     * @param int $amount
     *
     * @return $this
     */
    public function times(int $amount = 1)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param int $user_id
     *
     * @return $this
     */
    public function issued_to(int $user_id = null)
    {
        $this->issuedTo = $user_id;

        return $this;
    }

    /**
     * @param int $paymentMode
     * @param int $paymentTxnid
     * @param int $paymentAmount
     *
     * @return $this
     */
    public function payment($paymentMode = null, $paymentTxnid = null, $paymentAmount = null)
    {
        $this->paymentMode = $paymentMode;
        $this->paymentTxnid = $paymentTxnid;
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * @param int $amount
     *
     * @return $this
     */
    public function uses(int $amount = 1)
    {
        $this->uses = $amount;

        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     * @throws \Clarkeash\Doorman\Exceptions\DuplicateException
     */
    public function for(string $email)
    {
        if ($this->invite->where('for', strtolower($email))->first()) {
            throw new DuplicateException('You cannot create more than 1 invite code for an email');
        }

        $this->email = $email;

        return $this;
    }

    /**
     * @param \Carbon\Carbon $date
     *
     * @return $this
     */
    public function expiresOn(Carbon $date)
    {
        $this->expiry = $date;

        return $this;
    }

    /**
     * @param int $days
     *
     * @return $this
     */
    public function expiresIn($days = 14)
    {
        $this->expiry = Carbon::now(config('app.timezone'))->addDays($days)->endOfDay();

        return $this;
    }

    /**
     * @return \Clarkeash\Doorman\Models\BaseInvite
     */
    protected function build(): BaseInvite
    {
        $invite = app()->make(BaseInvite::class);
        $invite->code = Str::upper($this->manager->code());
        $invite->for = $this->email;
        $invite->max = $this->uses;
        $invite->valid_until = $this->expiry;
        $invite->user_to_id = $this->issuedTo;
        $invite->payment_mode = $this->paymentMode;
        $invite->payment_txnid = $this->paymentTxnid;
        $invite->payment_amount = $this->paymentAmount;

        return $invite;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws DuplicateException
     */
    public function make()
    {
        $invites = collect();

        if (!is_null($this->email) && $this->amount > 1) {
            throw new DuplicateException('You cannot create more than 1 invite code for an email');
        }

        for ($i = 0; $i < $this->amount; $i++) {
            $invite = $this->build();

            $invites->push($invite);

            $invite->save();
        }

        return $invites;
    }
}
