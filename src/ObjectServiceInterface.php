<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\AllowableActionsInterface;
use Dkd\PhpCmis\Data\BulkUpdateObjectIdAndChangeTokenInterface;
use Dkd\PhpCmis\Data\ContentStreamInterface;
use Dkd\PhpCmis\Data\ExtensionsDataInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\RenditionDataInterface;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use Dkd\PhpCmis\Enum\UnfileObject;
use Dkd\PhpCmis\Enum\VersioningState;

/**
 * Object Service interface.
 *
 * See the CMIS 1.0 and CMIS 1.1 specifications for details on the operations,
 * parameters, exceptions and the domain model.
 */
interface ObjectServiceInterface
{

    /**
     * Appends the content stream to the content of the document.
     *
     * The stream in contentStream is consumed but not closed by this method.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object. The repository might return a different/new object id
     * @param string $changeToken (optional) the last change token of this object that the client received.
     * The repository might return a new change token (default is null)
     * @param ContentStreamInterface $contentStream the content stream to append
     * @param boolean $isLastChunk indicates if this content stream is the last chunk
     * @param ExtensionsDataInterface $extension
     * @return void
     */
    public function appendContentStream(
        $repositoryId,
        $objectId,
        $changeToken,
        ContentStreamInterface $contentStream,
        $isLastChunk,
        ExtensionsDataInterface $extension
    );

    /**
     * Updates properties and secondary types of one or more objects.
     *
     * @param string $repositoryId the identifier for the repository
     * @param BulkUpdateObjectIdAndChangeTokenInterface[] $objectIdsAndChangeTokens
     * @param PropertiesInterface $properties
     * @param string[] $addSecondaryTypeIds the secondary types to apply
     * @param string[] $removeSecondaryTypeIds the secondary types to remove
     * @param ExtensionsDataInterface $extension
     * @return BulkUpdateObjectIdAndChangeTokenInterface[]
     */
    public function bulkUpdateProperties(
        $repositoryId,
        array $objectIdsAndChangeTokens,
        PropertiesInterface $properties,
        array $addSecondaryTypeIds,
        array $removeSecondaryTypeIds,
        ExtensionsDataInterface $extension
    );

    /**
     * Creates a document object of the specified type (given by the cmis:objectTypeId property)
     * in the (optionally) specified location.
     *
     * @param string $repositoryId the identifier for the repository
     * @param PropertiesInterface $properties the property values that must be applied to the newly
     * created document object
     * @param string $folderId if specified, the identifier for the folder that must be the parent folder
     * for the newly created document object
     * @param ContentStreamInterface $contentStream the content stream that must be stored for the newly
     * created document object
     * @param VersioningState $versioningState specifies what the versioning state of the newly created object
     * must be (default is VersioningState::MAJOR)
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface $addAces a list of ACEs that must be added to the newly created document object,
     * either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface $removeAces a list of ACEs that must be removed from the newly created document object,
     * either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionsDataInterface $extension
     * @return string
     */
    public function createDocument(
        $repositoryId,
        PropertiesInterface $properties,
        $folderId = null,
        ContentStreamInterface $contentStream = null,
        VersioningState $versioningState = VersioningState::MAJOR,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Creates a document object as a copy of the given source document in the (optionally) specified location.
     * @param string $repositoryId the identifier for the repository
     * @param string $sourceId the identifier for the source document
     * @param PropertiesInterface $properties the property values that must be applied to the newly
     * created document object
     * @param string $folderId if specified, the identifier for the folder that must be the parent folder for the
     * newly created document object
     * @param VersioningState $versioningState specifies what the versioning state of the newly created object
     * must be (default is VersioningState::MAJOR)
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface $addAces a list of ACEs that must be added to the newly created document object,
     * either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface $removeAces a list of ACEs that must be removed from the newly created document object,
     * either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionsDataInterface $extension
     * @return string
     */
    public function createDocumentFromSource(
        $repositoryId,
        $sourceId,
        PropertiesInterface $properties,
        $folderId = null,
        VersioningState $versioningState = VersioningState::MAJOR,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Creates a folder object of the specified type (given by the cmis:objectTypeId property) in
     * the specified location.
     *
     * @param string $repositoryId the identifier for the repository
     * @param PropertiesInterface $properties the property values that must be applied to the newly
     * created document object
     * @param string $folderId if specified, the identifier for the folder that must be the parent folder for the
     * newly created document object
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface $addAces a list of ACEs that must be added to the newly created document object,
     * either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface $removeAces a list of ACEs that must be removed from the newly created document object,
     * either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionsDataInterface $extension
     * @return string
     */
    public function createFolder(
        $repositoryId,
        PropertiesInterface $properties,
        $folderId,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Creates an item object of the specified type (given by the cmis:objectTypeId property).
     *
     * @param string $repositoryId the identifier for the repository
     * @param PropertiesInterface $properties the property values that must be applied to the newly
     * created document object
     * @param string $folderId if specified, the identifier for the folder that must be the parent folder for the
     * newly created document object
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface $addAces a list of ACEs that must be added to the newly created document object,
     * either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface $removeAces a list of ACEs that must be removed from the newly created document object,
     * either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionsDataInterface $extension
     * @return string
     */
    public function createItem(
        $repositoryId,
        PropertiesInterface $properties,
        $folderId = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Creates a policy object of the specified type (given by the cmis:objectTypeId property).
     *
     * @param string $repositoryId the identifier for the repository
     * @param PropertiesInterface $properties the property values that must be applied to the newly
     * created document object
     * @param string $folderId if specified, the identifier for the folder that must be the parent folder for the
     * newly created document object
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface $addAces a list of ACEs that must be added to the newly created document object,
     * either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface $removeAces a list of ACEs that must be removed from the newly created document object,
     * either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionsDataInterface $extension
     * @return string
     */
    public function createPolicy(
        $repositoryId,
        PropertiesInterface $properties,
        $folderId = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Creates a relationship object of the specified type (given by the cmis:objectTypeId property).
     *
     * @param string $repositoryId the identifier for the repository
     * @param PropertiesInterface $properties the property values that must be applied to the newly
     * created document object
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface $addAces a list of ACEs that must be added to the newly created document object,
     * either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface $removeAces a list of ACEs that must be removed from the newly created document object,
     * either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionsDataInterface $extension
     * @return string
     */
    public function createRelationship(
        $repositoryId,
        PropertiesInterface $properties,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Deletes the content stream for the specified document object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object. The repository might return a different/new object id
     * @param string $changeToken the last change token of this object that the client received. The repository might
     * return a new change token (default is null)
     * @param ExtensionsDataInterface $extension
     * @return void
     */
    public function deleteContentStream(
        $repositoryId,
        $objectId,
        $changeToken = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Deletes the specified object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param boolean $allVersions If true then delete all versions of the document, otherwise delete only the document
     * object specified (default is true)
     * @param ExtensionsDataInterface $extension
     * @return void
     */
    public function deleteObject(
        $repositoryId,
        $objectId,
        $allVersions = true,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Deletes the specified folder object and all of its child- and descendant-objects.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $folderId the identifier for the folder
     * @param boolean $allVersions If true then delete all versions of the document, otherwise delete only the
     * document object specified (default is true)
     * @param UnfileObject $unfileObjects defines how the repository must process file-able child- or
     * descendant-objects (default is UnfileObject.DELETE)
     * @param boolean $continueOnFailure If true, then the repository should continue attempting to perform this
     * operation even if deletion of a child- or descendant-object in the specified folder cannot be deleted
     * @param ExtensionsDataInterface $extension
     * @return array Returns a list of object ids that could not be deleted
     */
    public function deleteTree(
        $repositoryId,
        $folderId,
        $allVersions = true,
        UnfileObject $unfileObjects = UnfileObject::DELETE,
        $continueOnFailure = false,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Gets the list of allowable actions for an object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param ExtensionsDataInterface $extension
     * @return AllowableActionsInterface
     */
    public function getAllowableActions($repositoryId, $objectId, ExtensionsDataInterface $extension = null);

    /**
     * Gets the content stream for the specified document object, or gets a rendition stream for
     * a specified rendition of a document or folder object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string $streamId
     * @param int $offset
     * @param int $length
     * @param ExtensionsDataInterface $extension
     * @return ContentStreamInterface
     */
    public function getContentStream(
        $repositoryId,
        $objectId,
        $streamId,
        $offset,
        $length,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Gets the specified information for the object specified by id.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string $filter a comma-separated list of query names that defines which properties
     * must be returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if true, then the repository must return the allowable
     * actions for the object (default is false)
     * @param IncludeRelationships $includeRelationships indicates what relationships in which the object
     * participates must be returned (default is IncludeRelationships.NONE)
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     * matches this filter (default is "cmis:none")
     * @param boolean $includePolicyIds if true, then the repository must return the policy ids for
     * the object (default is false)
     * @param boolean $includeAcl if true, then the repository must return the ACL for the object (default is false)
     * @param ExtensionsDataInterface $extension
     * @return ObjectDataInterface
     */
    public function getObject(
        $repositoryId,
        $objectId,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = IncludeRelationships::NONE,
        $renditionFilter = 'cmis:none',
        $includePolicyIds = false,
        $includeAcl = false,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Gets the specified information for the object specified by path.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $path the path to the object
     * @param string $filter a comma-separated list of query names that defines which properties
     * must be returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if true, then the repository must return the allowable
     * actions for the object (default is false)
     * @param IncludeRelationships $includeRelationships indicates what relationships in which the object
     * participates must be returned (default is IncludeRelationships.NONE)
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     * matches this filter (default is "cmis:none")
     * @param boolean $includePolicyIds if true, then the repository must return the policy ids for
     * the object (default is false)
     * @param boolean $includeAcl if true, then the repository must return the ACL for the object (default is false)
     * @param ExtensionsDataInterface $extension
     * @return ObjectDataInterface
     */
    public function getObjectByPath(
        $repositoryId,
        $path,
        $filter,
        $includeAllowableActions,
        IncludeRelationships $includeRelationships,
        $renditionFilter,
        $includePolicyIds,
        $includeAcl,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Gets the list of properties for an object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string $filter a comma-separated list of query names that defines which properties
     * must be returned by the repository (default is repository specific)
     * @param ExtensionsDataInterface $extension
     * @return PropertiesInterface
     */
    public function getProperties(
        $repositoryId,
        $objectId,
        $filter = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Gets the list of associated renditions for the specified object.
     * Only rendition attributes are returned, not rendition stream.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     * matches this filter (default is "cmis:none")
     * @param int $maxItems
     * @param int $skipCount
     * @param ExtensionsDataInterface $extension
     * @return RenditionDataInterface[]
     */
    public function getRenditions(
        $repositoryId,
        $objectId,
        $renditionFilter = 'cmis:none',
        $maxItems = null,
        $skipCount = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Moves the specified file-able object from one folder to another.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object. The repository might return a different/new object id
     * @param string $targetFolderId the identifier for the target folder
     * @param string $sourceFolderId the identifier for the source folder
     * @param ExtensionsDataInterface $extension
     * @return void
     */
    public function moveObject(
        $repositoryId,
        $objectId,
        $targetFolderId,
        $sourceFolderId,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Sets the content stream for the specified document object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object. The repository might return a different/new object id
     * @param ContentStreamInterface $contentStream the content stream
     * @param boolean $overwriteFlag (optional) If true, then the repository must replace the existing content stream
     * for the object (if any) with the input content stream. If If false, then the repository must only set
     * the input content stream for the object if the object currently does not have a content stream (default is true)
     * @param string $changeToken the last change token of this object that the client received.
     * The repository might return a new change token (default is null)
     * @param ExtensionsDataInterface $extension
     * @return void
     */
    public function setContentStream(
        $repositoryId,
        $objectId,
        ContentStreamInterface $contentStream,
        $overwriteFlag = true,
        $changeToken = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Updates properties of the specified object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object. The repository might return a different/new object id
     * @param PropertiesInterface $properties the updated property values that must be applied to the object
     * @param string $changeToken (optional) the last change token of this object that the client received.
     * The repository might return a new change token (default is null)
     * @param ExtensionsDataInterface $extension
     * @return void
     */
    public function updateProperties(
        $repositoryId,
        $objectId,
        PropertiesInterface $properties,
        $changeToken = null,
        ExtensionsDataInterface $extension = null
    );
}