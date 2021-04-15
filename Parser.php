<?php


class Parser
{

    private string $strPage;
    private array $arrModule;

    public function __construct(string $url, string $pattern)
    {

        $this->strPage = $this->getStrPage($url);
        $this->arrModule = $this->getAllModule($this->strPage, $pattern);

    }

    /**
     * Get page text
     * @param string $url
     * @return bool|string
     * @throws Exception
     */
    private function getStrPage(string $url)
    {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.193 Safari/537.36");
        $pageResults = curl_exec($ch);
        if (!$pageResults) {
            throw new Exception("problem with server");
        }
        curl_close($ch);
        return $pageResults;
    }

    /**
     * Get the data you need from the page.
     * @param string $strPage
     * @param string $pattern
     * @return mixed
     * @throws Exception
     */
    private function getData(string $strPage, string $pattern)
    {
        preg_match($pattern, $strPage, $matches);
        if (!isset($matches[1])) {
            throw new Exception('match not found!!!');
        }
        return $matches[1];
    }

    /**
     * Get modules of data from the page.
     * @param string $strPage
     * @param string $pattern
     * @return array
     * @throws Exception
     */
    private function getAllModule(string $strPage, string $pattern)
    {

        preg_match_all($pattern, $strPage, $matches);
        if (count($matches[1]) === 0) {
            throw new Exception('matches not found!!!');
        }
        $arrRes = [];
        foreach ($matches[1] as $moduleStr) {
            $objModule = new stdClass();
            $objModule->textModule = $moduleStr;
            $arrRes[] = $objModule;
        }
        return $arrRes;
    }

    /**
     * Get need  data from modules.
     * @param array $arrPoint
     * @return array
     * @throws Exception
     */
    public function getNeedData(array $arrPoint)
    {

        foreach ($arrPoint as $arrPointSignature) {
            $pointName = $arrPointSignature[0];
            $pattern = $arrPointSignature[1];
            foreach ($this->arrModule as $objModule) {
                $objModule->$pointName = $this->getData($objModule->textModule, $pattern);
            }
        }

        return $this->arrModule;
    }

    /**
     * @param string $srcName
     * @param string $pattern
     * @return array
     * @throws Exception
     */
    public function getTextContent(string $srcName, string $pattern)
    {
        $arrRes = [];
        foreach ($this->arrModule as $objModule) {
            $url = $objModule->$srcName;
            $strPage = $this->getStrPage($url);
            $content = $this->getData($strPage, $pattern);
            $arrRes[] = $content;
        }
        return $arrRes;
    }

    /**
     * Download and save images.
     * @param string $srcImgName
     * @param string $path
     */
    public function getImg(string $srcImgName, string $path)
    {
        foreach ($this->arrModule as $objModule) {

            $url = $objModule->$srcImgName;
            if ($url == false) {
                continue;
            }
            //take name of img
            $pattern = '#http.*/(.*)$#';
            preg_match($pattern, $url, $match);
            $imgPathName = $path . '/' . $match[1];
            file_put_contents($imgPathName, file_get_contents($url));
        }
    }

}