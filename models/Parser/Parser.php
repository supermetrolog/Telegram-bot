<?php

namespace app\models\parser;

use Yii;

class Parser
{
    private const EXIST = 1;
    private const NO_DATA = 0;

    public function parse()
    {
        $file = file_get_contents(Yii::getAlias('@app') . '/components/info.html');
        \phpQuery::newDocument($file);
        $data = [];

        $data['period'] = $this->parsePeriod();
        $data['restricted'] = $this->parseList(ClassHTML::RESTRICT_LIST, ClassHTML::RESTRICT_DATA);
        $data['dtp'] = $this->parseList(ClassHTML::DTP_LIST, ClassHTML::DTP_DATA);
        $data['wanted'] = $this->parseList(ClassHTML::WANTED_LIST, ClassHTML::WANTED_DATA);
        $data['vehicle'] = $this->parseVehicle(ClassHTML::VEHICLE_LIST, ClassHTML::VEHICLE_DATA);
        \phpQuery::unloadDocuments();
        return $this->normalizeArray($data);
    }
    private function normalizeArray($data)
    {
        foreach ($data as $key => $item) {
            $data[$key] = $this->normalize($item);
        }
        return $data;
    }

    private function normalize($text)
    {
        return preg_replace('|[\s]+|s', ' ', $text);
    }
    private function parseList($listClass, $dataClass)
    {
        $attribute = $this->getAttribute($dataClass);
        if (!$this->dataExist($listClass)) {
            return self::NO_DATA;
        }
        $data = [];

        $elem = pq($listClass . ' > li');

        foreach ($elem as $key => $value) {
            $item = pq($value);

            $restrict = $item->find($dataClass . ' > li >' . ClassHTML::FIELD);
            foreach ($restrict as $restrict_key => $restrict_value) {
                $data[$key][$attribute[$restrict_key]] = $this->normalize($restrict_value->textContent);
            }
            if ($listClass === ClassHTML::RESTRICT_LIST) {
                $data[$key]['document_url'] = $item->find($dataClass . ' > li >' . ClassHTML::FIELD . ' > a')->attr('href');
            }
        }

        print_r($data);
        return $data;
    }
    private function parseVehicle()
    {
        if (!$this->dataExist(ClassHTML::VEHICLE_LIST)) {
            return self::NO_DATA;
        }
        $data = [];
        $data['model'] = pq(ClassHTML::VEHICLE_MODEL)->text();
        $data['year'] = pq(ClassHTML::VEHICLE_YEAR)->text();
        $data['chassis_number'] = pq(ClassHTML::VEHICLE_CHASSIS_NUMBER)->text();
        $data['vin'] = pq(ClassHTML::VEHICLE_VIN)->text();
        $data['body_number'] = pq(ClassHTML::VEHICLE_BODY_NUMBER)->text();
        $data['color'] = pq(ClassHTML::VEHICLE_COLOR)->text();
        $data['engine_voluem'] = pq(ClassHTML::VEHICLE_ENGINE_VOLUEM)->text();
        $data['power'] = pq(ClassHTML::VEHICLE_POWER)->text();
        $data['type'] = pq(ClassHTML::VEHICLE_TYPE)->text();
        print_r($data);
        return $data;
    }
    private function getAttribute($dataClass)
    {
        switch ($dataClass) {
            case ClassHTML::RESTRICT_DATA:
                return $this->getRestrictedAttribute();
                break;
            case ClassHTML::DTP_DATA:
                return $this->getDtpAttribute();
                break;
            case ClassHTML::WANTED_DATA:
                return $this->getWantedAttribute();
                break;
            default:
                echo 'No Attribute';
                break;
        }
    }

  
    private function getDtpAttribute()
    {
        return [
            0 => 'date',
            1 => 'type',
            2 => 'region',
            3 => 'scene',
            4 => 'model',
            5 => 'year',
            6 => 'opf',
            7 => 'ts_number'
        ];
    }
    private function getWantedAttribute()
    {
        return [
            0 => 'model',
            1 => 'year',
            2 => 'date',
            3 => 'region'
        ];
    }

    private function getRestrictedAttribute()
    {
        return [
            0 => 'model',
            1 => 'year',
            2 => 'date',
            3 => 'region',
            4 => 'by_whom',
            5 => 'type',
            6 => 'document',
            7 => 'phone',
            8 => 'gibdd_key'
        ];
    }

    private function dataExist($listClass)
    {
        $elem = $this->normalize(pq($listClass . ':has(li)')->text());
        if ($elem === "" || $elem === " ") {
            return self::NO_DATA;
        }
        return self::EXIST;
    }
    private function parsePeriod()
    {
        $period = [];

        $elem = pq(ClassHTML::PERIODS . ' > li > ' . ClassHTML::PERIOD_FROM);
        foreach ($elem as $key => $value) {
            $period[$key]['from'] = $this->normalize($value->textContent);
        }

        $elem = pq(ClassHTML::PERIODS . ' > li > ' . ClassHTML::PERIOD_TO);
        foreach ($elem as $key => $value) {
            $period[$key]['to'] = $this->normalize($value->textContent);
        }

        $elem = pq(ClassHTML::PERIODS . ' > li > ' . ClassHTML::PERSONE);
        foreach ($elem as $key => $value) {
            $period[$key]['person'] = $this->normalize($value->textContent);
        }

        $period['last_operation'] = pq(ClassHTML::PERIODS . ' > li:last > div')->text();
        print_r($period);
        return $period;
    }
}
