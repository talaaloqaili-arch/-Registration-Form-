<?php
// Object that holds one registration
class Registration
{
    public function __construct(
        public string $name,
        public string $email,
        public string $mobile,
        public string $governorate,
        public string $track,
        public array  $skills,
        public string $message
    ) 

    public function skillsText(): string { return implode(', ', $this->skills); }
}
