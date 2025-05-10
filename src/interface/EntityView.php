<?php

interface EntityView {
    public function place($record, ?WebModifier $modifier = null);
}
