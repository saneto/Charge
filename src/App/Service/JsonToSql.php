<?php
/**
 * Created by PhpStorm.
 * User: 10601450
 * Date: 22/02/2019
 * Time: 16:34
 */

namespace App\Service;


class JsonToSql
{
    function getQueryFromJson($json):String{
        $selectString = SelectPartialParser::parse($this->fieldPrefixesToClasses);
        $fromString = FromPartialParser::parse($this->className);
        $joinString = JoinPartialParser::parse($this->fieldPrefixesToClasses);

        $whereParsedRuleGroup = WherePartialParser::parse(
            $this->fieldsToProperties,
            $ruleGroup,
            $this->embeddableFieldsToProperties,
            $this->embeddableInsideEmbeddableFieldsToProperties,
            $this->className
        );
        $whereString = $whereParsedRuleGroup->getQueryString();
        $parameters = $whereParsedRuleGroup->getParameters();

        $orderString = OrderPartialParser::parse(
            $this->fieldsToProperties,
            $sortColumns,
            $this->embeddableFieldsToProperties,
            $this->embeddableInsideEmbeddableFieldsToProperties
        );

        $dqlString = preg_replace('/\s+/', ' ', $selectString.$fromString.$joinString.$whereString.$orderString);

        return new ParsedRuleGroup($dqlString, $parameters, $this->className);
    }

    function pSelect(array $fieldPrefixesToClasses = []): string
    {
        $selectString = 'SELECT '.static::OBJECT_WORD;

        foreach ($fieldPrefixesToClasses as $fieldPrefix => $associationClass) {
            $selectString .= sprintf(
                ', %s_%s',
                self::OBJECT_WORD,
                StringManipulator::replaceAllDots($fieldPrefix)
            );
        }

        return $selectString.' ';
    }

    function pFrom(array $fieldPrefixesToClasses = []): string
    {
        $selectString = 'SELECT '.static::OBJECT_WORD;

        foreach ($fieldPrefixesToClasses as $fieldPrefix => $associationClass) {
            $selectString .= sprintf(
                ', %s_%s',
                self::OBJECT_WORD,
                StringManipulator::replaceAllDots($fieldPrefix)
            );
        }

        return $selectString.' ';
    }

    function pWhere(array $fieldPrefixesToClasses = []): string
    {
        $selectString = 'SELECT '.static::OBJECT_WORD;

        foreach ($fieldPrefixesToClasses as $fieldPrefix => $associationClass) {
            $selectString .= sprintf(
                ', %s_%s',
                self::OBJECT_WORD,
                StringManipulator::replaceAllDots($fieldPrefix)
            );
        }

        return $selectString.' ';
    }

    function pJoint(array $fieldPrefixesToClasses = []): string
    {
        $selectString = 'SELECT '.static::OBJECT_WORD;

        foreach ($fieldPrefixesToClasses as $fieldPrefix => $associationClass) {
            $selectString .= sprintf(
                ', %s_%s',
                self::OBJECT_WORD,
                StringManipulator::replaceAllDots($fieldPrefix)
            );
        }

        return $selectString.' ';
    }

    function pWhereJoin(array $fieldPrefixesToClasses = []): string
    {
        $selectString = 'SELECT '.static::OBJECT_WORD;

        foreach ($fieldPrefixesToClasses as $fieldPrefix => $associationClass) {
            $selectString .= sprintf(
                ', %s_%s',
                self::OBJECT_WORD,
                StringManipulator::replaceAllDots($fieldPrefix)
            );
        }

        return $selectString.' ';
    }

}