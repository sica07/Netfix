<?php

namespace App\Validators;

class MovieValidator
{
    private $data;
    private $errors = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function isInvalid()
    {
        if (!isset($this->data->title) || !is_string($this->data->title)) {
            $this->errors[] = 'TITLE field missing or not a valid string';
        }
        if (isset($this->data->rating)) {
            if (!is_numeric($this->data->rating)) {
                $this->errors[] = 'RATING field is not a number';
            }
        }
        if (isset($this->data->poster)) {
            if (false === filter_var($this->data->poster, FILTER_VALIDATE_URL)) {
                $this->errors[] = 'POSTER field is not an url';
            }
        }
        if (isset($this->data->year)) {
            if (!is_numeric($this->data->year)) {
                $this->errors[] = 'YEAR field is not a number';
            }
        }
        if (isset($this->data->length)) {
            if (!is_numeric($this->data->length)) {
                $this->errors[] = 'LENGTH field is not a valid string';
            }
        }

        if (count($this->errors) > 0) {
            return implode('\n', $this->erros);
        }

        return false;
    }
}
