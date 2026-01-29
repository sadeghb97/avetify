<?php
namespace Avetify\Models;

use ReflectionProperty;

class DataModel {
    public function __construct($data){
        $this->hydrate($data);
    }

    protected function hydrate(array $data): void {
        foreach ($data as $key => $value) {

            if (!property_exists($this, $key)) {
                continue;
            }

            $refProp = new ReflectionProperty($this, $key);
            $refProp->setAccessible(true);

            $type = $refProp->getType();

            if ($type === null || $type->getName() === 'mixed') {
                $this->$key = $value;
                continue;
            }

            if ($value === null) {

                if ($type->allowsNull()) {
                    $this->$key = null;
                    continue;
                }

                switch ($type->getName()) {
                    case 'int':
                        $value = 0;
                        break;

                    case 'float':
                        $value = 0.0;
                        break;

                    case 'string':
                        $value = '';
                        break;

                    case 'bool':
                        $value = false;
                        break;

                    case 'array':
                        $value = [];
                        break;

                    default:
                        continue 2;
                }
            }

            switch ($type->getName()) {
                case 'int':
                    $value = (int) $value;
                    break;

                case 'float':
                    $value = (float) $value;
                    break;

                case 'bool':
                    $value = (bool) $value;
                    break;

                case 'string':
                    $value = (string) $value;
                    break;
            }

            $this->$key = $value;
        }
    }
}
