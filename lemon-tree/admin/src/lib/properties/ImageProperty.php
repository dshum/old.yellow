<?php namespace LemonTree\Properties;

class ImageProperty extends BaseProperty {

	protected $folderName = null;
	protected $hash = null;
	protected $folderPath = null;
	protected $folderWebPath = null;

	protected $maxSize = 8192;
	protected $maxWidth = null;
	protected $maxHeight = null;
	protected $allowedMimeTypes = array(
		'gif', 'jpeg', 'pjpeg', 'png',
	);

	protected $resize = null;
	protected $resizes = array();

	public function __construct($name) {
		parent::__construct($name);

		$this->
		addRule('max:'.$this->maxSize, 'Максимальный размер файла: '.$this->maxSize.' Кб')->
		addRule('mimes:'.join(',', $this->allowedMimeTypes), 'Допустимые форматы файла: GIF, JPG, PNG');

		return $this;
	}

	public static function create($name)
	{
		return new self($name);
	}

	public function getResizeValue($name = null)
	{
		return
			$name
			? str_replace(
				$this->getName(),
				$this->getName().'_'.$name,
				$this->getValue()
			)
			: $this->getValue();
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

	public function setMaxWidth($maxWidth)
	{
		$this->maxWidth = $maxWidth;

		return $this;
	}

	public function getMaxWidth()
	{
		return $this->maxWidth;
	}

	public function setMaxHeight($maxHeight)
	{
		$this->maxHeight = $maxHeight;

		return $this;
	}

	public function getMaxHeight()
	{
		return $this->maxHeight;
	}

	public function setResize($width, $height, $quality)
	{
		$this->resize = array($width, $height, $quality);

		return $this;
	}

	public function getResize()
	{
		return $this->resize;
	}

	public function addResize($name, $width, $height, $quality)
	{
		$this->resizes[$name] = array($width, $height, $quality);

		return $this;
	}

	public function getResizes()
	{
		return $this->resizes;
	}

	public function src($name = null)
	{
		return $this->path($name);
	}

	public function width($name = null)
	{
		if($this->exists($name)) {
			try {
				list(
					$width, $height, $type, $attr
				) = getimagesize($this->abspath($name));
				return $width;
			} catch (BaseException $e) {}
		}

		return 0;
	}

	public function height($name = null)
	{
		if($this->exists($name)) {
			try {
				list(
					$width, $height, $type, $attr
				) = getimagesize($this->abspath($name));
				return $height;
			} catch (BaseException $e) {}
		}

		return 0;
	}

	public function path($name = null)
	{
		return asset(
			trim(
				$this->getItemClass()->getFolder(),
				DIRECTORY_SEPARATOR
			)
			.DIRECTORY_SEPARATOR
			.$this->getResizeValue($name)
		);
	}

	public function abspath($name = null)
	{
		return
			public_path().DIRECTORY_SEPARATOR
			.trim(
				$this->getItemClass()->getFolder(),
				DIRECTORY_SEPARATOR
			)
			.DIRECTORY_SEPARATOR
			.$this->getResizeValue($name);
	}

	public function filename($name = null)
	{
		return basename($this->getResizeValue($name));
	}

	public function filesize($name = null)
	{
		return $this->exists($name) ? filesize($this->abspath($name)) : 0;
	}

	public function filesize_kb($name = null, $precision = 0)
	{
		return round($this->filesize($name) / 1024, $precision);
	}

	public function filesize_mb($name = null, $precision = 0)
	{
		return round($this->filesize($name) / 1024 / 1024, $precision);
	}

	public function exists($name = null)
	{
		return $this->getValue() && file_exists($this->abspath($name));
	}

	public function folder_path($name = null)
	{
		return dirname($this->abspath($name));
	}

	public function folder_exists($name = null)
	{
		return is_dir($this->folder_path($name));
	}

	public function set($field = null)
	{
		if ( ! $field) $field = $this->getName();

		$name = $this->getName();

		if (\Input::hasFile($field)) {

			$file = \Input::file($field);

			if ($file->isValid() && $file->getMimeType()) {

				$this->drop();

				$path = $file->getRealPath();
				$original = $file->getClientOriginalName();
				$extension = $file->getClientOriginalExtension();

				if ( ! $extension) $extension = 'txt';

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

				$hash = substr(md5(rand()), 0, 8);

				foreach ($this->resizes as $resizeName => $resize) {

					list($width, $height, $quality) = $resize;

					$resizeFilename = sprintf('%s_%s_%s.%s',
						$name,
						$resizeName,
						$hash,
						$extension
					);

					ImageUtils::resizeAndCopy(
						$path,
						$destination.DIRECTORY_SEPARATOR.$resizeFilename,
						$width,
						$height,
						$quality
					);

				}

				$filename = sprintf('%s_%s.%s',
					$name,
					$hash,
					$extension
				);

				if (is_array($this->resize)) {

					list($width, $height, $quality) = $this->resize;

					ImageUtils::resizeAndCopy(
						$path,
						$destination.DIRECTORY_SEPARATOR.$filename,
						$width,
						$height,
						$quality
					);

					unlink($path);

				} else {

					$file->move($destination, $filename);

				}

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

		foreach ($this->resizes as $name => $resize) {
			if ($this->exists($name)) {
				try {
					unlink($this->abspath($name));
				} catch (\Exception $e) {}
			}
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
			'src' => $this->src(),
			'width' => $this->width(),
			'height' => $this->height(),
		);

		return $scope;
	}

	public function getEditView()
	{
		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $this->getValue(),
			'readonly' => $this->getReadonly(),
			'exists' => $this->exists(),
			'src' => $this->src(),
			'width' => $this->width(),
			'height' => $this->height(),
			'filesize' => $this->filesize_kb(null, 1),
			'filename' => $this->filename(),
			'maxFilesize' => $this->getMaxSize(),
			'maxWidth' => $this->getMaxWidth(),
			'maxHeight' => $this->getMaxHeight(),
		);

		foreach ($this->resizes as $resizeName => $resize) {
			$scope['resizes'][] = [
				'name' => $resizeName,
				'exists' => $this->exists($resizeName),
				'src' => $this->src($resizeName),
				'width' => $this->width($resizeName),
				'height' => $this->height($resizeName),
				'filesize' => $this->filesize_kb($resizeName, 1),
				'filename' => $this->filename($resizeName),
			];
		}

		return $scope;
	}

	public function getElementSearchView()
	{
		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => \Input::get($this->getName()),
		);

		try {
			$view = $this->getClassName().'.elementSearch';
			return \View::make('admin::properties.'.$view, $scope);
		} catch (\Exception $e) {}

		return null;
	}

}
