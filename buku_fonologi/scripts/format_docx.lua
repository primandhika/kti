-- Start each main manuscript part on a new page after the first part.
local first_heading = true
function Header(element)
  if element.level == 1 then
    if first_heading then
      first_heading = false
    else
      return {
        pandoc.RawBlock('openxml', '<w:p><w:r><w:br w:type="page"/></w:r></w:p>'),
        element
      }
    end
  end
  return element
end

-- Editorial notes outside the book remain plain labels in the Word draft.
function Link(element)
  if element.target:match('%.md$') then
    return element.content
  end
  return element
end
