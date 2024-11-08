<?php

namespace Hiperdino\Anniversary\Helper;

class ExtraInfo
{
    protected Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function getAnniversaryInfo($extraInfo)
    {
        $extraInfoArray = json_decode($extraInfo ?: "", true);
        $hasAnniversaryInfo = false;

        if (!is_null($extraInfoArray)) {
            foreach ($extraInfoArray as $key => $arrayInfo) {
                if (isset($arrayInfo["code"]) && $arrayInfo["code"] == Config::CODE) {
                    $hasAnniversaryInfo = $extraInfoArray[$key];
                }
            }
        }

        if (!$hasAnniversaryInfo) {
            return false;
        }
        if (!isset($hasAnniversaryInfo["info"])) {
            return false;
        }

        $anniversaryInfo = [];
        foreach ($hasAnniversaryInfo["info"] as $infoItem) {
            $anniversaryInfo[$infoItem["key"]] = $infoItem["value"];
        }

        return $anniversaryInfo;
    }

    public function getExtraInfoWithAnniversary($extraInfo, $qty, $remove = false)
    {
        $extraInfoArray = json_decode($extraInfo ?: "", true);

        if (!is_null($extraInfoArray)) {
            foreach ($extraInfoArray as $key => $arrayInfo) {
                if (isset($arrayInfo["code"]) && ($arrayInfo["code"] == Config::CODE)) {
                    unset($extraInfoArray[$key]);
                }
            }
        }

        if (is_null($extraInfoArray)) $extraInfoArray = [];

        if (!$remove) {
            $extraInfoArray[] = [
                "code" => Config::CODE,
                "info" => [
                    [
                        "key" => "label",
                        "value" => $this->config->getRascaLabel()
                    ],
                    [
                        "key" => "qty",
                        "value" => "" . $qty . " uds"
                    ]
                ]
            ];
        }

        return json_encode($extraInfoArray);
    }
}
