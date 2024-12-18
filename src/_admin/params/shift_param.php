<?php

// Компонент отображения одно параметра согласно его типу
if (!isset($model)) {
    throw new Exception('В компонент params/render необходимо передать переменную $model с типом ShiftParamModel');
}
