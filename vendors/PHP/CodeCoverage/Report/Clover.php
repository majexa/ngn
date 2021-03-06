<?php
/**
 * PHP_CodeCoverage
 *
 * Copyright (c) 2009-2012, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    CodeCoverage
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2009-2012 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://github.com/sebastianbergmann/php-code-coverage
 * @since      File available since Release 1.0.0
 */

/**
 * Generates a Clover XML logfile from an PHP_CodeCoverage object.
 *
 * @category   PHP
 * @package    CodeCoverage
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2009-2012 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: 1.1.3
 * @link       http://github.com/sebastianbergmann/php-code-coverage
 * @since      Class available since Release 1.0.0
 */
class PHP_CodeCoverage_Report_Clover
{
    /**
     * @param  PHP_CodeCoverage $coverage
     * @param  string           $target
     * @param  string           $name
     * @return string
     */
    public function process(PHP_CodeCoverage $coverage, $target = NULL, $name = NULL)
    {
        $xmlDocument = new DOMDocument('1.0', 'UTF-8');
        $xmlDocument->formatOutput = TRUE;

        $xmlCoverage = $xmlDocument->createElement('coverage');
        $xmlCoverage->setAttribute('generated', (int)$_SERVER['REQUEST_TIME']);
        $xmlDocument->appendChild($xmlCoverage);

        $xmlProject = $xmlDocument->createElement('project');
        $xmlProject->setAttribute('timestamp', (int)$_SERVER['REQUEST_TIME']);

        if (is_string($name)) {
            $xmlProject->setAttribute('name', $name);
        }

        $xmlCoverage->appendChild($xmlProject);

        $packages = [];
        $report   = $coverage->getReport();
        unset($coverage);

        foreach ($report as $item) {
            $namespace = 'global';

            if (!$item instanceof PHP_CodeCoverage_Report_Node_File) {
                continue;
            }

            $xmlFile = $xmlDocument->createElement('file');
            $xmlFile->setAttribute('name', $item->getPath());

            $classes      = array_merge($item->getClasses(), $item->getTraits());
            $coverage     = $item->getCoverageData();
            $lines        = [];
            $ignoredLines = $item->getIgnoredLines();

            foreach ($classes as $className => $class) {
                $classStatements        = 0;
                $coveredClassStatements = 0;
                $coveredMethods         = 0;

                foreach ($class['methods'] as $methodName => $method) {
                    $methodCount        = 0;
                    $methodLines        = 0;
                    $methodLinesCovered = 0;

                    for ($i  = $method['startLine'];
                         $i <= $method['endLine'];
                         $i++) {
                        if (isset($ignoredLines[$i])) {
                            continue;
                        }

                        $add   = TRUE;
                        $count = 0;

                        if (isset($coverage[$i])) {
                            if ($coverage[$i] !== NULL) {
                                $classStatements++;
                                $methodLines++;
                            } else {
                                $add = FALSE;
                            }

                            $count = count($coverage[$i]);

                            if ($count > 0) {
                                $coveredClassStatements++;
                                $methodLinesCovered++;
                            }
                        } else {
                            $add = FALSE;
                        }

                        $methodCount = max($methodCount, $count);

                        if ($add) {
                            $lines[$i] = [
                              'count' => $count, 'type'  => 'stmt'
                            ];
                        }
                    }

                    if ($methodCount > 0) {
                        $coveredMethods++;
                    }

                    $lines[$method['startLine']] = [
                      'count' => $methodCount,
                      'crap'  => PHP_CodeCoverage_Util::crap(
                                   $method['ccn'],
                                   PHP_CodeCoverage_Util::percent(
                                     $methodLinesCovered,
                                     $methodLines
                                   )
                                 ),
                      'type'  => 'method',
                      'name'  => $methodName
                    ];
                }

                if (!empty($class['package']['namespace'])) {
                    $namespace = $class['package']['namespace'];
                }

                $xmlClass = $xmlDocument->createElement('class');
                $xmlClass->setAttribute('name', $className);
                $xmlClass->setAttribute('namespace', $namespace);

                if (!empty($class['package']['fullPackage'])) {
                    $xmlClass->setAttribute(
                      'fullPackage', $class['package']['fullPackage']
                    );
                }

                if (!empty($class['package']['category'])) {
                    $xmlClass->setAttribute(
                      'category', $class['package']['category']
                    );
                }

                if (!empty($class['package']['package'])) {
                    $xmlClass->setAttribute(
                      'package', $class['package']['package']
                    );
                }

                if (!empty($class['package']['subpackage'])) {
                    $xmlClass->setAttribute(
                      'subpackage', $class['package']['subpackage']
                    );
                }

                $xmlFile->appendChild($xmlClass);

                $xmlMetrics = $xmlDocument->createElement('metrics');
                $xmlMetrics->setAttribute('methods', count($class['methods']));
                $xmlMetrics->setAttribute('coveredmethods', $coveredMethods);
                $xmlMetrics->setAttribute('conditionals', 0);
                $xmlMetrics->setAttribute('coveredconditionals', 0);
                $xmlMetrics->setAttribute('statements', $classStatements);
                $xmlMetrics->setAttribute(
                  'coveredstatements', $coveredClassStatements
                );
                $xmlMetrics->setAttribute(
                  'elements',
                  count($class['methods']) +
                  $classStatements
                  /* + conditionals */);
                $xmlMetrics->setAttribute(
                  'coveredelements',
                  $coveredMethods +
                  $coveredClassStatements
                  /* + coveredconditionals */
                );
                $xmlClass->appendChild($xmlMetrics);
            }

            foreach ($coverage as $line => $data) {
                if ($data === NULL ||
                    isset($lines[$line]) ||
                    isset($ignoredLines[$line])) {
                    continue;
                }

                $lines[$line] = [
                  'count' => count($data), 'type' => 'stmt'
                ];
            }

            ksort($lines);

            foreach ($lines as $line => $data) {
                if (isset($ignoredLines[$line])) {
                    continue;
                }

                $xmlLine = $xmlDocument->createElement('line');
                $xmlLine->setAttribute('num', $line);
                $xmlLine->setAttribute('type', $data['type']);

                if (isset($data['name'])) {
                    $xmlLine->setAttribute('name', $data['name']);
                }

                if (isset($data['crap'])) {
                    $xmlLine->setAttribute('crap', $data['crap']);
                }

                $xmlLine->setAttribute('count', $data['count']);
                $xmlFile->appendChild($xmlLine);
            }

            $linesOfCode = $item->getLinesOfCode();

            $xmlMetrics = $xmlDocument->createElement('metrics');
            $xmlMetrics->setAttribute('loc', $linesOfCode['loc']);
            $xmlMetrics->setAttribute('ncloc', $linesOfCode['ncloc']);
            $xmlMetrics->setAttribute('classes', $item->getNumClasses());
            $xmlMetrics->setAttribute('methods', $item->getNumMethods());
            $xmlMetrics->setAttribute(
              'coveredmethods', $item->getNumTestedMethods()
            );
            $xmlMetrics->setAttribute('conditionals', 0);
            $xmlMetrics->setAttribute('coveredconditionals', 0);
            $xmlMetrics->setAttribute(
              'statements', $item->getNumExecutableLines()
            );
            $xmlMetrics->setAttribute(
              'coveredstatements', $item->getNumExecutedLines()
            );
            $xmlMetrics->setAttribute(
              'elements',
              $item->getNumMethods() +
              $item->getNumExecutableLines()
              /* + conditionals */
            );
            $xmlMetrics->setAttribute(
              'coveredelements',
              $item->getNumTestedMethods() +
              $item->getNumExecutedLines()
              /* + coveredconditionals */
            );
            $xmlFile->appendChild($xmlMetrics);

            if ($namespace == 'global') {
                $xmlProject->appendChild($xmlFile);
            } else {
                if (!isset($packages[$namespace])) {
                    $packages[$namespace] = $xmlDocument->createElement(
                      'package'
                    );

                    $packages[$namespace]->setAttribute('name', $namespace);
                    $xmlProject->appendChild($packages[$namespace]);
                }

                $packages[$namespace]->appendChild($xmlFile);
            }
        }

        $linesOfCode = $report->getLinesOfCode();

        $xmlMetrics = $xmlDocument->createElement('metrics');
        $xmlMetrics->setAttribute('files', count($report));
        $xmlMetrics->setAttribute('loc', $linesOfCode['loc']);
        $xmlMetrics->setAttribute('ncloc', $linesOfCode['ncloc']);
        $xmlMetrics->setAttribute(
          'classes', $report->getNumClasses()
        );
        $xmlMetrics->setAttribute('methods', $report->getNumMethods());
        $xmlMetrics->setAttribute(
          'coveredmethods', $report->getNumTestedMethods()
        );
        $xmlMetrics->setAttribute('conditionals', 0);
        $xmlMetrics->setAttribute('coveredconditionals', 0);
        $xmlMetrics->setAttribute(
          'statements', $report->getNumExecutableLines()
        );
        $xmlMetrics->setAttribute(
          'coveredstatements', $report->getNumExecutedLines()
        );
        $xmlMetrics->setAttribute(
          'elements',
          $report->getNumMethods() +
          $report->getNumExecutableLines()
          /* + conditionals */
        );
        $xmlMetrics->setAttribute(
          'coveredelements',
          $report->getNumTestedMethods() +
          $report->getNumExecutedLines()
          /* + coveredconditionals */
        );
        $xmlProject->appendChild($xmlMetrics);

        if ($target !== NULL) {
            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0777, TRUE);
            }

            return $xmlDocument->save($target);
        } else {
            return $xmlDocument->saveXML();
        }
    }
}
