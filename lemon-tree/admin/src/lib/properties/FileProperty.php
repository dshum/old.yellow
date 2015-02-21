<?php namespace LemonTree\Properties;

class FileProperty extends BaseProperty {

	protected $folderName = null;
	protected $hash = null;
	protected $folderPath = null;
	protected $folderWebPath = null;

	protected $maxSize = 8192;
	protected $allowedMimeTypes = array(
		'txt', 'pdf', 'xls', 'xlsx', 'ppt', 'doc', 'docx', 'xml',
		'gif', 'jpeg', 'pjpeg', 'png', 'tiff', 'ico',
		'zip', 'rar', 'tar',
	);

	public function __construct($name) {
		parent::__construct($name);

		$this->
		addRule('max:'.$this->maxSize, 'Максимальный размер файла: '.$this->maxSize.' Кб')->
		addRule('mimes:'.join(',', $this->allowedMimeTypes), 'Недопустимый формат файла');

		return $this;
	}

	public static function create($name)
	{
		return new self($name);
	}

	public function getRefresh()
	{
		return true;
	}

	public function setMaxSize($maxSize)
	{
		$this->maxSize = $maxSize;

		return $this;
	}

	public function getMaxSize()
	{
		return $this->maxSize;
	}

	public function path()
	{
		return asset(
			trim(
				$this->getItemClass()->getFolder(),
				DIRECTORY_SEPARATOR
			)
			.DIRECTORY_SEPARATOR
			.$this->getValue()
		);
	}

	public function abspath()
	{
		return
			public_path().DIRECTORY_SEPARATOR
			.trim(
				$this->getItemClass()->getFolder(),
				DIRECTORY_SEPARATOR
			)
			.DIRECTORY_SEPARATOR
			.$this->getValue();
	}

	public function filename()
	{
		return basename($this->getValue());
	}

	public function filesize()
	{
		return $this->exists() ? filesize($this->abspath()) : 0;
	}

	public function filesize_kb($precision = 0)
	{
		return round($this->filesize() / 1024, $precision);
	}

	public function filesize_mb($precision = 0)
	{
		return round($this->filesize() / 1024 / 1024, $precision);
	}

	public function exists()
	{
		return $this->getValue() && file_exists($this->abspath());
	}

	public function folder_path()
	{
		return dirname($this->abspath());
	}

	public function folder_exists()
	{
		return is_dir($this->folder_path());
	}

	public function set($field = null)
	{
		if ( ! $field) $field = $this->getName();

		$name = $this->getName();

		if (\Input::hasFile($field)) {

			$file = \Input::file($field);

			if ($file->isValid() && $file->getMimeType()) {

				$this->drop();

				$original = $file->getClientOriginalName();
				$extension = $file->getClientOriginalExtension();

				if ( ! $extension) $extension = 'txt';

				$filename = sprintf('%s_%s.%s',
					$name,
					substr(md5(rand()), 0, 8),
					$extension
				);

				$folderPath =
					public_path().DIRECTORY_SEPARATOR
					.trim(
						$this->element->getFolder(),
						DIRECTORY_SEPARATOR
					)
					.DIRECTORY_SEPARATOR;

				if ( ! file_exists($folderPath)) {
					mkdir($folderPath, 0755);
				}

				$folderHash =
					trim(
						$this->element->getFolderHash(),
						DIRECTORY_SEPARATOR
					);

				$destination = $folderHash
					? $folderPath.DIRECTORY_SEPARATOR.$folderHash
					: $folderPath;

				if ( ! file_exists($destination)) {
					mkdir($destination, 0755);
				}

				$file->move($destination, $filename);

				$this->element->$name = $folderHash
					? $folderHash.DIRECTORY_SEPARATOR.$filename
					: $filename;
			}

		} elseif (\Input::get($field.'_drop')) {

			$this->drop();

			$this->element->$name = null;

		}

		return $this;
	}

	public function drop()
	{
		if ($this->exists()) {
			try {
				unlink($this->abspath());
			} catch (\Exception $e) {}
		}

		if ($this->folder_exists()) {
			try {
				rmdir($this->folder_path());
			} catch (\Exception $e) {}
		}
	}

	public function getListView()
	{
		$scope = array(
			'exists' => $this->exists(),
			'path' => $this->path(),
			'filename' => $this->filename(),
			'filesize' => $this->filesize_kb(1),
		);

		try {
			$view = $this->getClassName().'.elementList';
			return \View::make('admin::properties.'.$view, $scope);
		} catch (\Exception $e) {}

		return null;
	}

	public function getEditView()
	{
		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $this->getValue(),
			'readonly' => $this->getReadonly(),
			'exists' => $this->exists(),
			'path' => $this->path(),
			'filesize' => $this->filesize_kb(1),
			'filename' => $this->filename(),
			'maxFilesize' => $this->getMaxSize(),
		);

		return $scope;
	}

}
