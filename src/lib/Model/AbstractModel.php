<?php

declare(strict_types=1);

namespace Kaliop\ContentDecorator\Model;

use DateTime;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\Exception as RepositoryException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Variation\Values\ImageVariation;
use Ibexa\Core\FieldType\Author\AuthorCollection;
use Ibexa\Core\FieldType\Author\Value as AuthorValue;
use Ibexa\Core\FieldType\BinaryFile\Value;
use Ibexa\Core\FieldType\BinaryFile\Value as BinaryFileValue;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Ibexa\Core\FieldType\Country\Value as CountryValue;
use Ibexa\Core\FieldType\Date\Value as DateValue;
use Ibexa\Core\FieldType\DateAndTime\Value as DateAndTimeValue;
use Ibexa\Core\FieldType\EmailAddress\Value as EmailAddressValue;
use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Ibexa\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use Ibexa\Core\FieldType\Integer\Value as IntegerValue;
use Ibexa\Core\FieldType\ISBN\Value as ISBNValue;
use Ibexa\Core\FieldType\Keyword\Value as KeywordValue;
use Ibexa\Core\FieldType\MapLocation\Value as MapLocationValue;
use Ibexa\Core\FieldType\Media\Value as MediaValue;
use Ibexa\Core\FieldType\Relation\Value as RelationValue;
use Ibexa\Core\FieldType\RelationList\Value as RelationListValue;
use Ibexa\Core\FieldType\Selection\Value as SelectionValue;
use Ibexa\Core\FieldType\TextBlock\Value as TextBlockValue;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Core\FieldType\Time\Value as TimeValue;
use Ibexa\Core\FieldType\Url\Value as UrlValue;
use Ibexa\Core\FieldType\User\Value as UserValue;
use Kaliop\ContentDecorator\Trait\IbexaRepositoryAwareTrait;
use Kaliop\ContentDecorator\Trait\ImageVariationAwareTrait;
use Kaliop\ContentDecorator\Trait\LoggerAwareTrait;
use Kaliop\ContentDecorator\Trait\ManagerAwareTrait;
use Kaliop\Contracts\ContentDecorator\Exception\ContentDecoratorException;
use Kaliop\Contracts\ContentDecorator\Injector\Type\IbexaRepositoryAwareInterface;
use Kaliop\Contracts\ContentDecorator\Injector\Type\ImageVariationAwareInterface;
use Kaliop\Contracts\ContentDecorator\Injector\Type\LoggerAwareInterface;
use Kaliop\Contracts\ContentDecorator\Injector\Type\ManagerAwareInterface;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

/**
 * This is an abstract class for all content decorators. It contains base implementation to get field values in a typesafe way.
 * By default, all core field types are supported.
 */
abstract class AbstractModel extends ContentDecorator implements IbexaRepositoryAwareInterface, ImageVariationAwareInterface, LoggerAwareInterface, ManagerAwareInterface
{
    use IbexaRepositoryAwareTrait;
    use ImageVariationAwareTrait;
    use LoggerAwareTrait;
    use ManagerAwareTrait;

    /**
     * @param Location|null $rootLocation
     * @param string[]|null $prioritizedLanguages
     *
     * @return iterable<Location>
     */
    public function getLocations(
        ?Location $rootLocation = null,
        ?array $prioritizedLanguages = null
    ): iterable {
        return $this->repository->getLocationService()->loadLocations(
            $this->getContent()->getContentInfo(),
            $rootLocation,
            $prioritizedLanguages,
        );
    }

    /**
     * @param string $fieldIdentifier
     *
     * @return bool
     */
    public function hasField(string $fieldIdentifier): bool
    {
        return (bool)$this->getContent()->getField($fieldIdentifier);
    }

    /**
     * @param string $field
     *
     * @return AuthorCollection
     *
     * @throws InvalidArgumentException
     */
    protected function getAuthorFieldValue(string $field): AuthorCollection
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof AuthorValue) {
            return $fieldValue->authors;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezauthor field');
        }
    }

    /**
     * @param string $field
     *
     * @return Value
     *
     * @throws InvalidArgumentException
     */
    protected function getBinaryFileFieldValue(string $field): BinaryFileValue
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof BinaryFileValue) {
            return $fieldValue;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezbinaryfile field');
        }
    }

    /**
     * @param string $field
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    protected function getCheckboxFieldValue(string $field): bool
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof CheckboxValue) {
            return $fieldValue->bool;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezboolean field');
        }
    }

    /**
     * @param string $field
     *
     * @return array<string, array{Name: string, Alpha2: string, Alpha3: string, IDC: int}>
     *
     * @throws InvalidArgumentException
     */
    protected function getCountryFieldValue(string $field): array
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof CountryValue) {
            /** @var array<string, array{Name: string, Alpha2: string, Alpha3: string, IDC: int}> $countries */
            $countries = $fieldValue->countries;

            return $countries;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezcountry field');
        }
    }

    /**
     * @param string $field
     *
     * @return DateTime|null
     *
     * @throws InvalidArgumentException
     */
    protected function getDateFieldValue(string $field): ?DateTime
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof DateValue) {
            return $fieldValue->date;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezdate field');
        }
    }

    /**
     * @param string $field
     *
     * @return DateTime|null
     *
     * @throws InvalidArgumentException
     */
    protected function getDateTimeFieldValue(string $field): ?DateTime
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof DateAndTimeValue) {
            return $fieldValue->value;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezdatetime field');
        }
    }

    /**
     * @param string $field
     *
     * @return string|null
     *
     * @throws InvalidArgumentException
     */
    protected function getEmailFieldValue(string $field): ?string
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof EmailAddressValue) {
            return $fieldValue->email;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezemail field');
        }
    }

    /**
     * @param string $field
     *
     * @return float|null
     *
     * @throws InvalidArgumentException
     */
    protected function getFloatFieldValue(string $field): ?float
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof FloatValue) {
            return $fieldValue->value;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezfloat field');
        }
    }

    /**
     * @param string $field
     *
     * @return ImageValue
     *
     * @throws InvalidArgumentException
     */
    protected function getImageFieldValue(string $field): ImageValue
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof ImageValue) {
            return $fieldValue;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezimage field');
        }
    }

    /**
     * @param string $field
     * @param string $variationName
     *
     * @return ImageVariation
     *
     * @throws InvalidArgumentException
     */
    protected function getImageFieldVariation(
        string $field,
        string $variationName = 'original'
    ): ImageVariation {
        $field = $this->getContent()->getField($field);
        if ($field?->getValue() instanceof ImageValue) {
            $variation = $this->imageVariationService->getVariation($field, $this->getContent()->getVersionInfo(), $variationName);
            if ($variation instanceof ImageVariation) {
                return $variation;
            }
        }

        throw new InvalidArgumentException('field', '$field should be an identifier of ezimage field');
    }

    /**
     * @param string $field
     *
     * @return ContentDecorator|null
     *
     * @throws InvalidArgumentException
     */
    protected function getImageAssetFieldValue(string $field): ?ContentDecorator
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof ImageAssetValue) {
            $contentId = $fieldValue->destinationContentId;
            if ($contentId) {
                try {
                    return $this->manager->loadContent($contentId);
                } catch (ContentDecoratorException $e) {
                    $this->logger->error(sprintf('Cannot load related content #%d for field "%s" in content #%d - %s', $contentId, $field, $this->getContent()->getId(), $e->getMessage()));
                }
            }

            return null;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezobjectrelation field');
        }
    }

    /**
     * @param string $field
     *
     * @return int|null
     *
     * @throws InvalidArgumentException
     */
    protected function getIntegerFieldValue(string $field): ?int
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof IntegerValue) {
            return $fieldValue->value;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezinteger field');
        }
    }

    /**
     * @param string $field
     *
     * @return string|null
     *
     * @throws InvalidArgumentException
     */
    protected function getISBNFieldValue(string $field): ?string
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof ISBNValue) {
            return $fieldValue->isbn ?: null;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezisbn field');
        }
    }

    /**
     * @param string $field
     *
     * @return string[]
     *
     * @throws InvalidArgumentException
     */
    protected function getKeywordFieldValue(string $field): array
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof KeywordValue) {
            return $fieldValue->values;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezkeyword field');
        }
    }

    /**
     * @param string $field
     *
     * @return MapLocationValue
     *
     * @throws InvalidArgumentException
     */
    protected function getMapLocationFieldValue(string $field): MapLocationValue
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof MapLocationValue) {
            return $fieldValue;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezgmaplocation field');
        }
    }

    /**
     * @param string $field
     *
     * @return MediaValue
     *
     * @throws InvalidArgumentException
     */
    protected function getMediaFieldValue(string $field): MediaValue
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof MediaValue) {
            return $fieldValue;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezmedia field');
        }
    }

    /**
     * @param string $field
     *
     * @return ContentDecorator|null
     *
     * @throws InvalidArgumentException
     */
    protected function getRelationFieldValue(string $field): ?ContentDecorator
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof RelationValue) {
            $contentId = $fieldValue->destinationContentId;
            if ($contentId) {
                try {
                    return $this->manager->loadContent($contentId);
                } catch (ContentDecoratorException $e) {
                    $this->logger->error(sprintf('Cannot load related content #%d for field "%s" in content #%d - %s', $contentId, $field, $this->getContent()->getId(), $e->getMessage()));
                }
            }

            return null;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezobjectrelation field');
        }
    }

    /**
     * @param string $field
     *
     * @return ContentDecorator[]
     *
     * @throws InvalidArgumentException
     * @throws ContentDecoratorException
     */
    protected function getRelationListFieldValue(string $field): array
    {
        $relatedContents = [];

        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof RelationListValue) {
            foreach ($fieldValue->destinationContentIds as $contentId) {
                try {
                    $relatedContents[] = $this->repository->getContentService()->loadContent($contentId);
                } catch (RepositoryException $e) {
                    $this->logger->error(sprintf('Cannot load related content #%d for field "%s" in content #%d - %s', $contentId, $field, $this->getContent()->getId(), $e->getMessage()));
                }
            }

            return $this->manager->decorateMultiple($relatedContents);
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezobjectrelationlist field');
        }
    }

    /**
     * @param string $field
     *
     * @return array<int, string>   Keys are selection option identifiers and values are option names.
     *
     * @throws InvalidArgumentException
     */
    protected function getSelectionFieldValue(string $field): array
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof SelectionValue) {
            $fieldSettings = $this->getContent()->getContentType()->getFieldDefinition($field)?->fieldSettings;
            $language = $this->getContent()->getDefaultLanguageCode();

            $options = $fieldSettings['multilingualOptions'][$language] ?? $fieldSettings['options'] ?? [];

            $values = [];
            foreach ($fieldValue->selection as $id) {
                $values[$id] = $options[$id] ?? null;
            }

            return $values;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezselection field');
        }
    }

    /**
     * @param string $field
     *
     * @return string|null
     *
     * @throws InvalidArgumentException
     */
    protected function getTextBlockFieldValue(string $field): ?string
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof TextBlockValue) {
            return $fieldValue->text;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of eztext field');
        }
    }

    /**
     * @param string $field
     *
     * @return string|null
     *
     * @throws InvalidArgumentException
     */
    protected function getTextLineFieldValue(string $field): ?string
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof TextLineValue) {
            return $fieldValue->text;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezstring field');
        }
    }

    /**
     * @param string $field
     *
     * @return int|null
     *
     * @throws InvalidArgumentException
     */
    protected function getTimeFieldValue(string $field): ?int
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof TimeValue) {
            return $fieldValue->time;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of eztime field');
        }
    }

    /**
     * @param string $field
     *
     * @return UrlValue
     *
     * @throws InvalidArgumentException
     */
    protected function getUrlFieldValue(string $field): UrlValue
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof UrlValue) {
            return $fieldValue;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezurl field');
        }
    }

    /**
     * @param string $field
     *
     * @return UserValue
     *
     * @throws InvalidArgumentException
     */
    protected function getUserFieldValue(string $field): UserValue
    {
        $fieldValue = $this->getContent()->getFieldValue($field);
        if ($fieldValue instanceof UserValue) {
            return $fieldValue;
        } else {
            throw new InvalidArgumentException('field', '$field should be an identifier of ezuser field');
        }
    }
}
