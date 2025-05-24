<?php
namespace Avetify\Interface;

interface EntityView {
    public function place($record, ?WebModifier $modifier = null);
}
