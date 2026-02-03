<?php

namespace V03413\BepusdtPhpSdk;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Bepusdt
{

    private string $api;
    private string $token;

    const defaultTradeType = 'usdt.trc20';
    const defaultFiat      = 'CNY';
    const timeout          = 1200;

    // 可选交易类型，更新不一定及时，完整参考：https://github.com/v03413/BEpusdt/blob/main/docs/trade-type.md
    const TRADE_TYPE = [
        // Tron
        'usdt.trc20',
        'usdc.trc20',
        'tron.trx',

        // Ethereum
        'usdt.erc20',
        'usdc.erc20',
        'ethereum.eth',

        // Polygon
        'usdt.polygon',
        'usdc.polygon',

        // BSC
        'usdt.bep20',
        'usdc.bep20',
        'bsc.bnb',

        // Aptos
        'usdt.aptos',
        'usdc.aptos',

        // Solana
        'usdt.solana',
        'usdc.solana',

        // X-Layer
        'usdt.xlayer',
        'usdc.xlayer',

        // Arbitrum-One
        'usdt.arbitrum',
        'usdc.arbitrum',

        // Base
        'usdc.base',

        // Plasma
        'usdt.plasma',
    ];

    // 可选法币类型
    const FIAT = ['CNY', 'USD', 'EUR', 'GBP', 'JPY'];

    public function __construct(string $api, string $token)
    {
        $this->api   = rtrim($api, '/');
        $this->token = $token;
    }

    /**
     * 创建交易订单
     * @throws Exception
     */
    public function createTransaction(
        string $orderId,
        float  $amount,
        string $notifyUrl,
        string $redirectUrl,
        string $tradeType = self::defaultTradeType,
        string $fiat = self::defaultFiat,
        string $rate = '',
        string $address = '',
        string $name = '',
        int    $timeout = self::timeout,
    ): array
    {
        $params = [
            'order_id'     => $orderId,
            'amount'       => $amount,
            'trade_type'   => $tradeType,
            'fiat'         => $fiat,
            'notify_url'   => $notifyUrl,
            'redirect_url' => $redirectUrl,
            'rate'         => $rate,
            'address'      => $address,
            'name'         => $name,
            'timeout'      => $timeout,
        ];
        $url    = $this->api . '/api/v1/order/create-transaction';

        $params['signature'] = self::sign($params, $this->token);

        return $this->_postJson($url, $params);
    }

    /**
     * 取消交易订单
     * @throws Exception
     */
    public function cancelTransaction(string $tradeId): array
    {
        $params = ['trade_id' => $tradeId,];
        $url    = $this->api . '/api/v1/order/cancel-transaction';

        $params['signature'] = self::sign($params, $this->token);

        return $this->_postJson($url, $params);
    }

    /**
     * @throws Exception
     */
    public function notify(): array
    {
        $post = json_decode(file_get_contents('php://input'), true);
        if (!is_array($post)) {

            throw new Exception("回调通知数据 格式错误");
        }

        if (empty($post['signature'])) {

            throw new Exception("回调通知签名 数据缺失");
        }

        if ($this->sign($post, $this->token) !== $post['signature']) {

            throw new Exception("回调通知签名 验证失败");
        }

        return $post;
    }

    protected function sign(array $params, string $token): string
    {
        ksort($params);

        $sign = '';

        foreach ($params as $key => $val) {
            if ($val == '') continue;
            if ($key != 'signature') {
                if ($sign != '') {
                    $sign .= "&";

                }

                $sign .= "$key=$val";
            }
        }

        return md5($sign . $token);
    }

    /**
     * @throws Exception
     */
    private function _postJson(string $url, array $data): array
    {
        $client = new Client([
            'timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'User-Agent'   => 'v03413/bepusdt-php-sdk',
            ]
        ]);

        try {
            $response = $client->post($url, ['json' => $data]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new Exception('请求失败: ' . $e->getMessage());
        }
    }
}