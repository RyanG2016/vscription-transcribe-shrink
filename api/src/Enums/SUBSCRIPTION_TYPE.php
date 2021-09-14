<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class SUBSCRIPTION_TYPE extends Enum
{

    /**
     * Needs manual input by system admin
     */
    const NOT_DEFINED = 0;

    /**
     * Full access for the platform
     *  -	Access to full platform for monthly subscription done through Zoho Subscriptions API
        -	If Org wants STT, they can purchase minutes separately
        -	Use existing roles for access
     */
    const PLATFORM = 1; // ALL ACCESS

    /**
     *  -	Upload and Administrative Capabilities
        -	No STT option
        -	Billed translationally per minute as per billing rates through Zoho Books API
        -	Use Existing Roles for access

     */
    const TRANSCRIPTION_SERVICES = 2;

    /**
     *	Limited platform access
        -	Ability to upload file
        -	All uploaded files are subject to available STT minutes
        -   **Unable to upload files and not send to STT**
        -   Billing done directly through Authorize.net API
        -   Requires new roles:
        -	**stt_author** – Can only upload if there is sufficient STT minutes left
        -	**stt_typist** – Can only upload if there is sufficient STT minutes left and has full transcribe capabilities
        -	**stt_admin** - Can only upload if there is sufficient STT minutes left and has review transcribe capabilities and access to organization settings

     */
    const SPEECH_TO_TEXT = 3;

}