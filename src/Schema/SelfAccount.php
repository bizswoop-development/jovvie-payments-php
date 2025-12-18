<?php

namespace JovviePayments\Schema;

class SelfAccount implements \JsonSerializable
{
    /**
     * @deprecated since 1.2.0, Use $account->stripeAccountId instead.
     */
    public string $id;
    public string $platformPublicKey;
    public Account $account;

    public function __construct($data)
    {
        $this->id = $data->id;
        $this->platformPublicKey = $data->platformPublicKey;

        $this->account = new Account($data->account);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'platformPublicKey' => $this->platformPublicKey,
            'account' => $this->account->jsonSerialize(),
        ];
    }
}
