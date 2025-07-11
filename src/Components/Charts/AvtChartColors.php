<?php
namespace Avetify\Components\Charts;

class AvtChartColors {
    public const defaultColors = [
        '#FF6384',
        '#36A2EB',
        '#FFCE56',
        '#4BC0C0',
        '#9966FF',
        '#FF9F40',
        '#C9CBCF',
        '#FF6B6B',
        '#6BFFB8',
        '#B86BFF',
        '#6BB8FF',
        '#FFD36B',
        '#8AFF6B',
        '#6BFFA9',
        '#FF6BDE',
        '#6BFFFA',
        '#FFA56B',
        '#A56BFF',
        '#6B9AFF',
        '#D66BFF',
    ];

    public const alterColors = [
        '#CC516A',
        '#2B82BB',
        '#CCAA45',
        '#3B9999',
        '#7D52CC',
        '#CC7F33',
        '#A5A7AA',
        '#CC5656',
        '#52CC94',
        '#9552CC',
        '#5295CC',
        '#CCA950',
        '#6ECC52',
        '#52CC88',
        '#CC52B2',
        '#52CCC7',
        '#CC844F',
        '#844FCC',
        '#527ACC',
        '#AA52CC',
    ];

    public static function getDefaultColors(int $size) : array {
        return array_slice(self::defaultColors, 0, $size);
    }

    public static function getAlterColors(int $size) : array {
        return array_slice(self::alterColors, 0, $size);
    }
}
